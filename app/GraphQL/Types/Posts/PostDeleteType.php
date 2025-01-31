<?php

namespace App\GraphQL\Types\Posts;

use App\Models\Post;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PostDeleteType extends GraphQLType
{
    protected $attributes = [
        'name' => 'PostDelete',
        'description' => 'Response Post Delete',
        'model' => Post::class,
    ];

    public function fields(): array
    {
        return [
            'status' => [
                'name' => 'status',
                'type' => Type::boolean(),
                'description' => 'Status'
            ]
        ];
    }
}
