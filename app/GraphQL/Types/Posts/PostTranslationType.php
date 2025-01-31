<?php

namespace App\GraphQL\Types\Posts;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PostTranslationType extends GraphQLType
{
    protected $attributes = [
        'name' => 'PostTranslation',
        'description' => 'Post Translation'
    ];

    public function fields(): array
    {
        return [
            'language' => [
                'type' => Type::string(),
                'description' => 'Language'
            ],
            'post_id' => [
                'type' => Type::int(),
                'description' => 'Reference Post'
            ],
            'title' => [
                'type' => Type::string(),
                'description' => 'Title Translation'
            ],
            'content' => [
                'type' => Type::string(),
                'description' => 'Content Translation'
            ],
            'meta_title' => [
                'type' => Type::string(),
                'description' => 'Meta Title Translation'
            ],
            'meta_description' => [
                'type' => Type::string(),
                'description' => 'Meta Description Translation'
            ]
        ];
    }
}
