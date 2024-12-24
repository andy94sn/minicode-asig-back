<?php

namespace App\GraphQL\Types\Pages;

use App\Models\Page;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PageType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Page',
        'description' => 'Pages Type',
        'model' => Page::class
    ];

    public function fields(): array
    {
        return [
            'token' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Token',
            ],
            'status' => [
                'type' => Type::boolean(),
                'description' => 'Status',
            ],
            'title' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Title'
            ],
            'slug' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'SEO-friendly Page URL'
            ],
            'type' => [
                'type' => Type::string(),
                'description' => 'Page Type'
            ],
            'translations' => [
                'type' => Type::listOf(GraphQL::type('PageTranslation')),
                'description' => 'Translations Page',
            ],
            'sections' => [
                'type' => Type::listOf(GraphQL::type('Section')),
                'description' => 'Sections Page'
            ]
        ];
    }
}
