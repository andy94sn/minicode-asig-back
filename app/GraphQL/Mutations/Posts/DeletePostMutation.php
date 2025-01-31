<?php

namespace App\GraphQL\Mutations\Posts;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Post;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class DeletePostMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deletePost',
        'description' => 'Delete Post',
        'model' => Post::class
    ];

    public function type(): Type
    {
        return GraphQL::type('PostDelete');
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
        $lang     = $args['lang'] ?? 'ro';

        try{
            $auth     = Admin::find(request()->auth['sub']);
            $token    = HelperService::clean($args['token']);
            $post     = Post::where('token', $token)->first();

            if(!$post) {
                return new Error(HelperService::message($lang, 'found'));
            }elseif(!$auth){
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-blog')) {
                return new Error(HelperService::message($lang, 'permission'));
            }

            if($post->delete()){
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
