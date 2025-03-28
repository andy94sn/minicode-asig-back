<?php

namespace App\GraphQL\Mutations\Admins;

use App\Models\Admin;
use App\Models\Contact;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class DeleteAdminMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteAdmin',
        'description' => 'Delete Admin',
        'model' => Admin::class
    ];

    public function type(): Type
    {
        return GraphQL::type('AdminDelete');
    }

    public function args(): array
    {
        return [
            'token' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Token'
            ],
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
            $admin = Admin::where('token', $token)->first();

            if(!$admin) {
                return new Error(HelperService::message($lang, 'found'));
            }elseif(!$auth) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-admins')) {
                return new Error(HelperService::message($lang, 'permission'));
            }

            if($admin->delete()){
                return [
                    'status' => true
                ];
            }

            return [
                'status' => false
            ];
        }catch(\Exception $exception){
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
