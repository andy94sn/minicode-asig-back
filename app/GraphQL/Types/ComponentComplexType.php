<?php

namespace App\GraphQL\Types;

use App\Models\Component;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ComponentComplexType extends GraphQLType
{
    protected $attributes = [
        'name' => 'ComponentComplex',
        'description' => 'Component Complex Page',
        'model' => Component::class
    ];

    public function fields(): array
    {
        return [
            'token' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Component Token'
            ],
            'title' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Component Title'
            ],
            'key' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Component Key'
            ],
            'type' => [
                'type' => Type::string(),
                'description' => 'Component Type'
            ],
            'order' => [
                'type' => Type::int(),
                'description' => 'Component Order'
            ],
            'status' => [
                'type' => Type::boolean(),
                'description' => 'Component Status'
            ],
            'content' => [
                'type' => Type::listOf(GraphQL::type('Translation')),
                'description' => 'Component Translations',
                'args' => [
                    'lang' => [
                        'name' => 'lang',
                        'type' => new nonNull(GraphQL::type('EnumLanguage')),
                        'description' => 'Language Page'
                    ]
                ],
                'resolve' => function ($root, $args) {
                    $lang = $args['lang'] ?? null;
                    $translations = $root['content'];
                    if ($lang) {
                        return array_filter($translations, function ($translation) use ($lang) {
                            return $translation['lang'] === $lang;
                        });
                    }
                    return $translations;
                }
            ],
            'children' => [
                'name' => 'children',
                'type' => Type::listOf(GraphQL::type('ComponentComplex')),
                'description' => 'Component Children'
            ]
        ];
    }
}
