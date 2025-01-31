<?php

namespace App\GraphQL\Queries\Permissions;

use App\Models\Admin;
use App\Models\Permission;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class AuthPermissionsQuery extends Query
{
    protected $attributes = [
        'name' => 'getAuthPermissions',
        'description' => 'Return Auth Permissions (Returnarea Permisiunilor Administratorului Autentificat)',
        'model' => Permission::class
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Permission'));
    }

    /**
     * @throws Error
     */
    public function resolve($root, $args)
    {
        $lang = $args['lang'] ?? 'ro';

        try{
            $auth = Admin::find(request()->auth['sub']);

            if(!$auth){
                return new Error(HelperService::message($lang, 'denied'));
            }

            $permissions = $auth->getPermissionsViaRoles();

            return $permissions->map(function ($permission) {
                return [
                    'token' => $permission->token,
                    'name' => $permission->name,
                    'description' => $permission->description
                ];
            })->toArray();
        }catch(\Exception $exception){
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
