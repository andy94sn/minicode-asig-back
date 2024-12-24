<?php

namespace App\GraphQL\Types\Components;

use App\Models\Component;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ComponentType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Component',
        'description' => 'Component Section',
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
                'type' => GraphQL::type('ComponentEnum'),
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
                'description' => 'Component Translations'
            ],
            'children' => [
                'name' => 'children',
                'type' => Type::listOf(GraphQL::type('Component')),
                'description' => 'Component Children'
            ]
        ];
    }
}
