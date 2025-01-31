<?php

namespace App\GraphQL\Mutations\Categories;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Role;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdateCategoryMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateCategory',
        'description' => 'Update Category'
    ];

    public function type(): Type
    {
        return GraphQL::type('Category');
    }

    public function args(): array
    {
        return [
            'token' => [
                'name' => 'token',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Token'
            ],
            'path' => [
                'name' => 'path',
                'type' => Type::string(),
                'description' => 'Image'
            ],
            'slug' => [
                'name' => 'slug',
                'type' => Type::string(),
                'description' => 'Slug'
            ],
            'translations' => [
                'name' => 'translations',
                'type' => Type::listOf(GraphQL::type('CategoryTranslationInput')),
                'description' => 'Translations'
            ],
            'status' => [
                'name' => 'status',
                'type' => Type::boolean(),
                'description' => 'Status'
            ],
            'order' => [
                'name' => 'order',
                'type' => Type::int(),
                'description' => 'Status'
            ]
        ];
    }

    /**
     * @throws Error
     */
    public function resolve($root, $args)
    {
        try{
            $lang   = $args['lang'] ?? 'ro';
            $auth   = Admin::find(request()->auth['sub']);
            $token  = HelperService::clean($args['token']);
            $status = (bool)$args['status'];
            $order  = $args['order'];
            $slug   = $args['slug'];
            $image  = null;
            if(!empty($args['path'])){
                $image = HelperService::clean($args['path']);
            }

            $category = Category::where('token', $token)->first();

            if(!$category){
                return new Error(HelperService::message($lang, 'found'));
            }elseif(!$auth){
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-blog')){
                return new Error(HelperService::message($lang, 'permission'));
            }

            $input = [
                'status' => $status,
                'order'  => $order,
                'slug'   => $slug
            ];

            if($image){
                $input['image'] = $image;
            }

            $category->update($input);

            if(count($args['translations']) > 0){
                foreach ($args['translations'] as $translation) {
                    $existingTranslation = $category->translations()->where('language', $translation['language'])->first();

                    if ($existingTranslation) {
                        $existingTranslation->update([
                            'title' => $translation['title'],
                            'meta_title' => $translation['meta_title'],
                            'meta_description' => $translation['meta_description']
                        ]);
                    }
                }
            }

            $category->load('posts');

            return $category;
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
