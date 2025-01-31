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

class UpdateProfileMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateProfile',
        'description' => 'Update Profile'
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

            $admin = Admin::where('token', $token)->first();

            if(!$admin){
                return new Error(HelperService::message($lang, 'found'));
            }elseif(!$auth){
                return new Error(HelperService::message($lang, 'denied'));
            }

            if ($password && $password !== $passwordConfirmation) {
                return new Error(HelperService::message($lang, 'password_mismatch'));
            }

            $admin->update([
                'name' => $name,
                'status' => $status,
                'password' => $password
            ]);

            return $admin;
        }catch(\Exception $exception){
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
