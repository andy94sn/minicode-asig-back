<?php

namespace App\GraphQL\Mutations\Admins;

use App\Models\Admin;
use App\Models\Role;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdateAdminMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateAdmin',
        'description' => 'Update Admin'
    ];

    public function type(): Type
    {
        return GraphQL::type('Admin');
    }

    public function args(): array
    {
        return [
            'token' => [
                'name' => 'token',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Token'
            ],
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
                'description' => 'Name'
            ],
            'idno' => [
                'name' => 'idno',
                'type' => Type::string(),
                'description' => 'IDNO'
            ],
            'status' => [
                'name' => 'status',
                'type' => Type::boolean(),
                'description' => 'Status'
            ],
            'password' => [
                'name' => 'password',
                'type' => Type::string(),
                'description' => 'Password'
            ],
            'password_confirmation' => [
                'name' => 'password_confirmation',
                'type' => Type::string(),
                'description' => 'Password Confirmation'
            ],
            'role' => [
                'name' => 'role',
                'type' => Type::string(),
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
            $auth = Admin::find(request()->auth['sub']);
            $token = HelperService::clean($args['token']);
            $name = HelperService::clean($args['name']);
            $status = $args['status'] ?? true;
            $password = $args['password'] ?? '';
            $passwordConfirmation = $args['password_confirmation'] ?? '';
            $idno = HelperService::clean($args['idno'] ?? '');

            $admin = Admin::where('token', $token)->first();

            if(!$admin){
                return new Error(HelperService::message($lang, 'found'));
            }elseif(!$auth){
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-admins')){
                return new Error(HelperService::message($lang, 'permission'));
            }

            if ($password && $password !== $passwordConfirmation) {
                return new Error(HelperService::message($lang, 'password_mismatch'));
            }

            $admin->update([
                'name' => $name,
                'status' => $status,
                'password' => $password,
                'idno' => $idno
            ]);

            if($args['role']){
                $admin->assignRole($args['role']);
            }

            return $admin;
        }catch(\Exception $exception){
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
