<?php

namespace App\GraphQL\Mutations\Categories;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Contact;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class DeleteCategoryMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteCategory',
        'description' => 'Delete Category',
        'model' => Category::class
    ];

    public function type(): Type
    {
        return GraphQL::type('CategoryDelete');
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
        try{
            $lang = $args['lang'] ?? 'ro';
            $auth = Admin::find(request()->auth['sub']);
            $token = HelperService::clean($args['token']);
            $category = Category::where('token', $token)->first();

            if(!$category) {
                return new Error(HelperService::message($lang, 'found'));
            }elseif(!$auth){
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-blog')) {
                return new Error(HelperService::message($lang, 'permission'));
            }

            if($category->delete()){
                return [
                    'status' => true
                ];
            }

            return [
                'status' => false
            ];
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            $lang = $args['lang'] ?? 'ro';
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
