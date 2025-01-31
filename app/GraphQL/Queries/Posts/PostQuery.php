<?php

namespace App\GraphQL\Queries\Posts;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Post;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class PostQuery extends Query
{
    protected $attributes = [
        'name' => 'getPost',
        'description' => 'Return Post Data',
        'model' => Post::class
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
            ]
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

            if (!$auth){
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-blog')) {
                throw new Error(HelperService::message($lang, 'permission'));
            }

            $post = Post::where('token', $token)->first();
            $category = Category::find($post->category_id);

            if($category){
                $post->category = $category->token;
            }else{
                $post->category = null;
            }

            return $post;
        }catch(\Exception $exception){
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }

    }
}
