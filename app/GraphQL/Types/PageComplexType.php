<?php

namespace App\GraphQL\Types;

use App\Models\Page;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PageComplexType extends GraphQLType
{
    protected $attributes = [
        'name' => 'PageComplex',
        'description' => 'Pages Complex Type',
        'model' => Page::class
    ];

    public function fields(): array
    {
        return [
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
                'description' => 'slug'
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
                'type' => Type::listOf(GraphQL::type('SectionComplex')),
                'description' => 'Sections Page',
                'args' => [
                    'lang' => [
                        'name' => 'lang',
                        'type' => new nonNull(GraphQL::type('EnumLanguage')),
                        'description' => 'Language Page'
                    ]
                ],
                'resolve' => function ($root, $args) {
                    return $root->sections;
                }
            ]
        ];
    }
}

