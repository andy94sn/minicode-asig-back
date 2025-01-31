<?php

namespace App\GraphQL\Mutations\Posts;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Post;
use App\Models\Role;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreatePostMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createPost',
        'description' => 'Create Post'
    ];

    public function type(): Type
    {
        return GraphQL::type('Post');
    }

    public function args(): array
    {
        return [
            'name' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Name'
            ],
            'category' => [
                'name' => 'category',
                'type' => Type::string(),
                'description' => 'Category'
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
            'order' => [
                'name' => 'order',
                'type' => Type::int(),
                'description' => 'Order'
            ],
            'author' => [
                'name' => 'author',
                'type' => Type::string(),
                'description' => 'Author'
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
        $lang = $args['lang'] ?? 'ro';

        try{
            $auth     = Admin::find(request()->auth['sub']);
            $name    = HelperService::clean($args['name']);
            $author   = HelperService::clean($args['author']);
            $slug     = HelperService::slugify($name);
            $categoryToken = $args['category'];
            $status   = $args['status'];
            $order    = $args['order'] ?? 1;
            $tags     = $args['tags'];
            $publish  = date('Y-m-d H:i:s');
            $image    = null;

            if(Post::where('slug', $slug)->exists()) {
                return new Error(HelperService::message($lang, 'exists'));
            }elseif (!$auth){
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-blog')) {
                return new Error(HelperService::message($lang, 'permission'));
            }

            $author = Admin::where('token', $author)->first();

            if(!empty($categoryToken)){
                $category = Category::where('token',  HelperService::clean($categoryToken))->first();
            }else{
                $category = null;
            }


            if($args['path']){
                $image = HelperService::clean($args['path']);
            }

            $post = Post::create([
                'name'   => $name,
                'slug'    => $slug,
                'author'  => $author->name,
                'status'  => $status,
                'image'   => $image,
                'order'   => $order,
                'tags'    => $tags,
                'published_at'  => $publish,
                'category_id' => $category ? $category->id : null
            ]);

            if(count($args['translations']) > 0){
                foreach ($args['translations'] as $translation){
                    $post->translations()->create([
                        'post_id' => $post->id,
                        'title' => $translation['title'],
                        'language' => $translation['language']
                    ]);
                }
            }

            return $post;
        }catch(\Exception $exception){
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
