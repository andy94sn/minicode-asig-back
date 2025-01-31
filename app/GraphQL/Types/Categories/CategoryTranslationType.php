<?php

namespace App\GraphQL\Types\Categories;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CategoryTranslationType extends GraphQLType
{
    protected $attributes = [
        'name' => 'CategoryTranslation',
        'description' => 'Category Translation'
    ];

    public function fields(): array
    {
        return [
            'language' => [
                'type' => Type::string(),
                'description' => 'Language'
            ],
            'category_id' => [
                'type' => Type::int(),
                'description' => 'Reference Category'
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
