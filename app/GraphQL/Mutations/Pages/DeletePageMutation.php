<?php

namespace App\GraphQL\Mutations\Pages;

use App\Models\Admin;
use App\Models\Page;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Mutation;

class DeletePageMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deletePage',
        'description' => 'Delete Page',
    ];

    public function type(): Type
    {
        return Type::boolean();
    }

    public function args(): array
    {
        return [
            'token' => [
                'name' => 'token',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Token Page'
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
            $page = Page::where('token', $token)->first();

            if(!$page){
                return new Error(HelperService::message($lang, 'found'));
            }elseif(!$auth && !$auth->is_super){
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-content')){
                return new Error(HelperService::message($lang, 'permission'));
            }

            if($page->delete()){
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
