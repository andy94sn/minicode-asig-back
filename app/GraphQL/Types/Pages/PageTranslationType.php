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
                'description' => 'Language Page',
            ],
            'title' => [
                'type' => Type::string(),
                'description' => 'Title of the page translation'
            ],
            'content' => [
                'type' => Type::string(),
                'description' => 'Content of the page translation'
            ],
            'meta_title' => [
                'type' => Type::string(),
                'description' => 'Meta title of the page translation'
            ],
            'meta_description' => [
                'type' => Type::string(),
                'description' => 'Meta description of the page translation'
            ],
            'meta_keywords' => [
                'type' => Type::string(),
                'description' => 'Meta keywords of the page translation'
            ]
        ];
    }
}
