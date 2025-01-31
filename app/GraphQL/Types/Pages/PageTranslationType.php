<?php

namespace App\GraphQL\Types\Pages;

use App\Models\PageTranslation;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PageTranslationType extends GraphQLType
{
    protected $attributes = [
        'name' => 'PageTranslation',
        'description' => 'Translations Page',
        'model' => PageTranslation::class
    ];

    public function fields(): array
    {
        return [
            'language' => [
                'type' => Type::string(),
                'description' => 'Language',
            ],
            'title' => [
                'type' => Type::string(),
                'description' => 'Title'
            ],
            'content' => [
                'type' => Type::string(),
                'description' => 'Content'
            ],
            'meta_title' => [
                'type' => Type::string(),
                'description' => 'Meta Title'
            ],
            'meta_description' => [
                'type' => Type::string(),
                'description' => 'Meta Description'
            ],
            'meta_keywords' => [
                'type' => Type::string(),
                'description' => 'Meta Keywords'
            ]
        ];
    }
}
