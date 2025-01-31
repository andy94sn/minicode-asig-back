<?php

namespace App\GraphQL\Mutations\Categories;

use App\Models\Admin;
use App\Models\Category;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateCategoryMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createCategory',
        'description' => 'Create Category'
    ];

    public function type(): Type
    {
        return GraphQL::type('Category');
    }

    public function args(): array
    {
        return [
            'name' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Name'
            ],
            'path' => [
                'name' => 'path',
                'type' => Type::string(),
                'description' => 'Image'
            ],
            'translations' => [
                'name' => 'translations',
                'type' => Type::listOf(GraphQL::type('CategoryTranslationInput')),
                'description' => 'Translations'
            ],
            'order' => [
                'name' => 'order',
                'type' => Type::int(),
                'description' => 'Order'
            ],
            'status' => [
                'name' => 'status',
                'type' => Type::boolean(),
                'description' => 'Status'
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
            $auth    = Admin::find(request()->auth['sub']);
            $name    = HelperService::clean($args['name']);
            $slug    = HelperService::slugify($name);
            $status  = $args['status'] ? 1 : 0;
            $order   = $args['order'] ?? 1;

            if(Admin::where('name', $name)->exists()) {
                return new Error(HelperService::message($lang, 'exists'));
            }elseif (!$auth){
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-blog')) {
                return new Error(HelperService::message($lang, 'permission'));
            }elseif(empty($name)){
                return new Error(HelperService::message($lang, 'invalid'));
            }

            if($args['path']){
                $image   = HelperService::clean($args['path']);
            }else{
                $image   = null;
            }

            $category = Category::create([
                'name' => $name,
                'image' => $image,
                'slug' => $slug,
                'status' => $status,
                'order' => $order
            ]);

            if(count($args['translations']) > 0){
                foreach ($args['translations'] as $translation){
                    $category->translations()->create([
                        'category_id' => $category->id,
                        'title' => $translation['title'],
                        'language' => $translation['language']
                    ]);
                }
            }

            $category->load('posts');

            return $category;
        }catch(\Exception $exception){
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
