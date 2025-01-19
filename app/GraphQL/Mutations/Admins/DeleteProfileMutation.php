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

class DeleteProfileMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteProfile',
        'description' => 'Delete Profile',
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
                'description' => 'Token Value'
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
            }elseif(!$auth && !$auth->is_super) {
                return new Error(HelperService::message($lang, 'denied'));
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
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
