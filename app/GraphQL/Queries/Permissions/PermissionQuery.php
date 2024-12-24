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

class PermissionQuery extends Query
{
    protected $attributes = [
        'name' => 'getPermissions',
        'description' => 'Return Permissions',
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

            if(!$auth && $auth->is_super){
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-admins')) {
                return new Error(HelperService::message($lang, 'permission'));
            }

            return Permission::all();
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
