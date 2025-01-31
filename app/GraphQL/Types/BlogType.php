<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class BlogType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Blog',
        'description' => 'Blog',
    ];

    public function fields(): array
    {
        return [
            'categories' => [
                'type' => Type::listOf(GraphQL::type('Category')),
                'description' => 'Categories with posts',
            ],
            'posts' => [
                'type' => Type::listOf(GraphQL::type('Post')),
                'description' => 'Uncategorized posts',
            ]
        ];
    }
}



