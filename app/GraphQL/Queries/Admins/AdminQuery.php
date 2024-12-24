<?php

namespace App\GraphQL\Queries\Admins;

use App\Models\Admin;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class AdminQuery extends Query
{
    protected $attributes = [
        'name' => 'getAdmin',
        'description' => 'Admin',
        'model' => Admin::class
    ];

    public function type(): Type
    {
        return GraphQL::type('Admin');
    }

    public function args(): array
    {
        return [
            'token' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Token'
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

            if (!$auth) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-admins')) {
                return new Error(HelperService::message($lang, 'permission'));
            }

            return Admin::where('token', $token)->first();
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }

    }
}
