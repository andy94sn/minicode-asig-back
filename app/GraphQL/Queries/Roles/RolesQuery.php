<?php

namespace App\GraphQL\Queries\Roles;

use App\Models\Admin;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use App\Models\Role;

class RolesQuery extends Query
{
    protected $attributes = [
        'name' => 'getRoles',
        'description' => 'Return Roles',
        'model' => Role::class
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Role'));
    }

    /**
     * @throws Error
     */
    public function resolve($root, $args)
    {
        $lang = $args['lang'] ?? 'ro';

        try{
            $auth = Admin::find(request()->auth['sub']);

            if (!$auth && !$auth->is_super){
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-admins')) {
                return new Error(HelperService::message($lang, 'permission'));
            }

            return Role::all();
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
