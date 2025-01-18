<?php

namespace App\GraphQL\Mutations\Admins;

use App\Models\Admin;
use App\Models\Role;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateAdminMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createAdmin',
        'description' => 'Create Admin'
    ];

    public function type(): Type
    {
        return GraphQL::type('Admin');
    }

    public function args(): array
    {
        return [
            'name' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Name'
            ],
            'email' => [
                'name' => 'email',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Email'
            ],
            'password' => [
                'name' => 'password',
                'type' => Type::string(),
                'description' => 'Password'
            ],
            'password_confirmation' => [
                'name' => 'password_confirmation',
                'type' => Type::string(),
                'description' => 'Confirmation Password'
            ],
            'status' => [
                'name' => 'status',
                'type' => Type::boolean(),
                'description' => 'Status'
            ],
            'role' => [
                'name' => 'role',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Role'
            ]
        ];
    }

    /**
     * @throws Error
     */
    public function resolve($root, $args)
    {
        $lang = $args['lang'] ?? 'ro';

        try{
            $auth  = Admin::find(request()->auth['sub']);
            $role  = Role::where('name', trim($args['role']))->first();
            $name  = HelperService::clean($args['name']);
            $email = HelperService::clean($args['email']);
            $password = HelperService::clean($args['password']);
            $status  = $args['status'];
            Log::info($status);

            if(Admin::where('email', $email)->exists()) {
                return new Error(HelperService::message($lang, 'exists'));
            }elseif(!$role) {
                return new Error(HelperService::message($lang, 'found').' - Role');
            }elseif (!$auth){
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-admins') || ($role == 'dev-admin' && !$auth->is_super)) {
                return new Error(HelperService::message($lang, 'permission'));
            }elseif(empty($email) || empty($name) ){
                return new Error(HelperService::message($lang, 'invalid'));
            }

            $admin = Admin::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'status' => $status
            ]);

            $admin->assignRole($role);

            return $admin;
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
