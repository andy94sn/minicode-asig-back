<?php

namespace App\GraphQL\Types\Posts;

use App\Models\Post;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PostType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Post',
        'description' => 'Post Type',
        'model' => Post::class
    ];

    public function fields(): array
    {
        return [
            'token' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Token',
            ],
            'category' => [
                'type' => Type::string(),
                'description' => 'Token Category',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'Name'
            ],
            'slug' => [
                'type' => Type::string(),
                'description' => 'Slug',
            ],
            'image' => [
                'type' => Type::string(),
                'description' => 'Image',
            ],
            'tags' => [
                'type' => Type::listOf(GraphQL::type('Tag')),
                'description' => 'Tags',
            ],
            'translations' => [
                'type' => Type::listOf(GraphQL::type('PostTranslation')),
                'description' => 'Post Translations'
            ],
            'author' => [
                'type' => Type::string(),
                'description' => 'Author',
            ],
            'published_at' => [
                'type' => Type::string(),
                'description' => 'Published Date',
            ],
            'order' => [
                'type' => Type::int(),
                'description' => 'Order'
            ],
            'status' => [
                'type' => Type::string(),
                'description' => 'Status'
            ],
        ];
    }
}
