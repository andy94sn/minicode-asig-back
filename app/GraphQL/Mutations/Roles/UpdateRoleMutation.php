<?php


namespace App\GraphQL\Mutations\Roles;

use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;

class UpdateRoleMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateRole',
        'description' => 'Update Role'
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
                'description' => 'Token'
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
        $auth = Admin::find(request()->auth['sub']);
        $token = HelperService::clean($args['token']);
        $lang = $args['lang'] ?? 'ro';

        $role = Role::where('token', $token)->first();
        $inputs = $args['permissions'] ?? [];
        $permissions = array();

        if(!$auth){
            return new Error(HelperService::message($lang, 'denied'));
        }elseif (!$auth->hasPermissionTo('manage-permissions')){
            return new Error(HelperService::message($lang, 'permission'));
        }elseif(!$role){
            return new Error(HelperService::message($lang, 'found').'- Role');
        }

        try{
            if (!empty($inputs)) {
                foreach($inputs as $input){
                    $permission = Permission::where('token', $input)->first();

                    if ($permission) {
                        $permissions[] = $permission;
                    }
                }
            }

            if (!empty($permissions)) {
                $role->syncPermissions($permissions);
            }

            return $role;
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error('Something went wrong');
        }
    }
}
