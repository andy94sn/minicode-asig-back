<?php

namespace App\GraphQL\Types\Pages;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\InputType;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PageTranslationInputType extends InputType
{
    protected $attributes = [
        'name' => 'PageTranslationInput',
        'description' => 'Input Translation',
    ];

    public function fields(): array
    {
        return [
            'language' => [
                'type' => GraphQL::type('LanguageEnum'),
                'description' => 'Language',
            ],
            'title' => [
                'type' => Type::string(),
                'description' => 'Title',
            ],
            'content' => [
                'type' => Type::string(),
                'description' => 'Content',
            ],
            'meta_title' => [
                'type' => Type::string(),
                'description' => 'Meta Title',
            ],
            'meta_description' => [
                'type' => Type::string(),
                'description' => 'Meta Description',
            ],
            'meta_keywords' => [
                'type' => Type::string(),
                'description' => 'Meta Keyword',
            ],
        ];
    }
}
