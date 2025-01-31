<?php

namespace App\GraphQL\Mutations\Roles;

use App\Models\Admin;
use App\Models\Role;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;

class DeleteRoleMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteRole',
        'description' => 'Delete Role'
    ];

    public function type(): Type
    {
        return GraphQL::type('Role');
    }

    public function args(): array
    {
        return [
            'token' => [
                'name' => 'token',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Token Role'
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

            $role = Role::where('token', $token)->first();
            $adminWithRole = Admin::whereHas('roles', function ($query) use ($role) {
                $query->where('roles.id', $role->id);
            })->exists();

            if(!$auth) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-permissions')){
                return new Error(HelperService::message($lang, 'permission'));
            }elseif(!$role){
                return new Error(HelperService::message($lang, 'found'));
            }elseif($adminWithRole) {
                return new Error(HelperService::message($lang, 'role'));
            }

            $role->delete();
            return $role;
        }catch(\Exception $exception){
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}

