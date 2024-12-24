<?php

namespace App\GraphQL\Types\Components;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ComponentFieldType extends GraphQLType
{
    protected $attributes = [
        'name' => 'ComponentType',
        'description' => 'A component that contains various fields'
    ];

    public function fields(): array
    {
        return [
            'translations' => [
                'type' => Type::listOf(GraphQL::type('Translation')),
                'description' => 'Translations'
            ],
            'title' => [
                'type' => GraphQL::type('Field'),
                'description' => 'Title Field'
            ],
            'bold' => [
                'type' => GraphQL::type('Field'),
                'description' => 'Bold Field'
            ],
            'break' => [
                'type' => GraphQL::type('Field'),
                'description' => 'Break Line Field'
            ],
            'link' => [
                'type' => GraphQL::type('Field'),
                'description' => 'URL field'
            ],
            'blank' => [
                'type' => GraphQL::type('Field'),
                'description' => 'New Tab Field'
            ],
            'background' => [
                'type' => GraphQL::type('Field'),
                'description' => 'Background Field'
            ],
            'alt' => [
                'type' => GraphQL::type('Field'),
                'description' => 'Alt Field'
            ],
            'button' => [
                'type' => GraphQL::type('Field'),
                'description' => 'Button Field'
            ],
            'question' => [
                'type' => GraphQL::type('Field'),
                'description' => 'Question Field'
            ],
            'answer' => [
                'type' => GraphQL::type('Field'),
                'description' => 'Answer Field'
            ]
        ];
    }
}
