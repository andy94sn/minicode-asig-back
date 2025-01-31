<?php

namespace App\GraphQL\Mutations\Contacts;

use App\Models\Admin;
use App\Models\Contact;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class DeleteContactMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteContact',
        'description' => 'Delete Contact',
    ];

    public function type(): Type
    {
        return GraphQL::type('ContactDelete');
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
            $contact = Contact::where('token', $token)->first();

            if(!$contact) {
                return new Error(HelperService::message($lang, 'found'));
            }elseif(!$auth) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-contacts')) {
                return new Error(HelperService::message($lang, 'permission'));
            }

            if($contact->delete()){
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
