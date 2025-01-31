<?php

namespace App\GraphQL\Mutations\Posts;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Post;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdatePostMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updatePost',
        'description' => 'Update Post'
    ];

    public function type(): Type
    {
        return GraphQL::type('Post');
    }

    public function args(): array
    {
        return [
            'token' => [
                'name' => 'token',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Token'
            ],
            'category' => [
                'name' => 'category',
                'type' => Type::string(),
                'description' => 'Category Token'
            ],
            'slug' => [
                'name' => 'slug',
                'type' => Type::string(),
                'description' => 'Slug'
            ],
            'order' => [
                'name' => 'order',
                'type' => Type::int(),
                'description' => 'Order'
            ],
            'path' => [
                'name' => 'path',
                'type' => Type::string(),
                'description' => 'Image'
            ],
            'translations' => [
                'name' => 'translations',
                'type' => Type::listOf(GraphQL::type('PostTranslationInput')),
                'description' => 'Translations'
            ],
            'tags' => [
                'name' => 'tags',
                'type' => Type::listOf(GraphQL::type('TagInput')),
                'description' => 'Tags'
            ],
            'status' => [
                'name' => 'status',
                'type' => Type::string(),
                'description' => 'Status'
            ]
        ];
    }

    /**
     * @throws Error
     */
    public function resolve($root, $args)
    {
        $lang   = $args['lang'] ?? 'ro';

        try{
            $token  = HelperService::clean($args['token']);
            $categoryToken  = $args['category'];
            $slug  = HelperService::clean($args['slug']);
            $auth     = Admin::find(request()->auth['sub']);
            $status   = $args['status'];
            $order    = $args['order'];
            $tags     = $args['tags'];
            $image    = null;

            if(!empty($args['path'])){
                $image = HelperService::clean($args['path']);
            }

            $post = Post::where('token', $token)->first();

            if(!$post){
                return new Error(HelperService::message($lang, 'found'));
            }elseif(!$auth){
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-blog')){
                return new Error(HelperService::message($lang, 'permission'));
            }

            $input = [
                'tags'    => $tags,
                'status' => $status,
                'order' => $order,
                'slug'   => $slug
            ];

            if($categoryToken){
                $category= Category::where('token', HelperService::clean($categoryToken))->first();
                if($category){
                    $input['category_id'] = $category->id;
                }
            }

            if($image){
                $input['image'] = $image;
            }

            $post->update($input);

            if(count($args['translations']) > 0){
                foreach ($args['translations'] as $translation) {
                    $post->translations()->updateOrCreate(
                        ['language' => $translation['language']],
                        [
                            'post_id' => $post->id,
                            'title' => $translation['title'],
                            'meta_title' => $translation['meta_title'],
                            'meta_description' => $translation['meta_description'],
                            'content' => $translation['content'],
                            'language' => $translation['language']
                        ]
                    );
                }
            }

            return $post;
        }catch(\Exception $exception){
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
