<?php

namespace App\GraphQL\Types\Components;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TranslationType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Translation',
        'description' => 'Component Type Translation'
    ];

    public function fields(): array
    {
        return [
            'lang' => [
                'type' => Type::string(),
                'description' => 'Language'
            ],
            'question' => [
                'type' => Type::string(),
                'description' => 'Question'
            ],
            'answer' => [
                'type' => Type::string(),
                'description' => 'Question'
            ],
            'value' => [
                'type' => Type::string(),
                'description' => 'Text'
            ],
            'caption' => [
                'type' => Type::string(),
                'description' => 'Sub-Title'
            ],
            'alt' => [
                'type' => Type::string(),
                'description' => 'Alt Image'
            ],
            'link' => [
                'type' => Type::string(),
                'description' => 'Link Button'
            ],
            'label' => [
                'type' => Type::string(),
                'description' => 'Label Input'
            ],
            'placeholder' => [
                'type' => Type::string(),
                'description' => 'Placeholder Input'
            ],
            'background' => [
                'type' => Type::boolean(),
                'description' => 'Background Image'
            ],
            'icon' => [
                'type' => Type::boolean(),
                'description' => 'Card Icon'
            ],
            'editor' => [
                'type' => Type::boolean(),
                'description' => 'Editor'
            ],
            'bold' => [
                'type' => Type::boolean(),
                'description' => 'Bold Text'
            ],
            'break' => [
                'type' => Type::boolean(),
                'description' => 'Break line'
            ],
            'blank' => [
                'type' => Type::boolean(),
                'description' => 'New Window'
            ]
        ];
    }
}
