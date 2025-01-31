<?php

namespace App\GraphQL\Mutations\Roles;

use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateRoleMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createRole',
        'description' => 'Create Role'
    ];

    public function type(): Type
    {
        return GraphQL::type('Role');
    }

    public function args(): array
    {
        return [
            'name' => [
                'name' => 'name',
                'type' => Type::string()
            ],
            'permissions' => [
                'name' => 'permissions',
                'type' => Type::listOf(GraphQL::type('PermissionInput')),
                'description' => 'List Permissions'
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
            $name = HelperService::slugify(HelperService::clean($args['name']));

            $isRole = Role::where('name', $name)->exists();
            $description = HelperService::clean($args['name']);
            $inputs = $args['permissions'] ?? [];
            $permissions = array();

            if($isRole) {
                return new Error(HelperService::message($lang, 'exists'));
            }elseif(!$auth) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-permissions')){
                return new Error(HelperService::message($lang, 'permission'));
            }

            $role = Role::create([
                'name' => $name,
                'description' => $description,
                'guard_name' => 'api'
            ]);

            if (!empty($inputs)) {
                foreach($inputs as $input){
                    $permission = Permission::where('token', $input)->first();
                    if($permission){
                        $permissions[] = $permission;
                    }
                }
                $role->syncPermissions($permissions);
            }

            return $role;
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
