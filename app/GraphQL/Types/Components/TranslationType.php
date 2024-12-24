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
            'value' => [
                'type' => Type::string(),
                'description' => 'Value'
            ],
            'caption' => [
                'type' => Type::string(),
                'description' => 'Sub-Title Card'
            ],
            'question' => [
                'type' => Type::string(),
                'description' => 'Question FAQ'
            ],
            'answer' => [
                'type' => Type::string(),
                'description' => 'Answer FAQ'
            ],
            'label' => [
                'type' => Type::string(),
                'description' => 'Label Input'
            ],
            'placeholder' => [
                'type' => Type::string(),
                'description' => 'Placeholder Input'
            ],
            'type' => [
                'type' => Type::string(),
                'description' => 'Type Input'
            ],
            'link' => [
                'type' => Type::string(),
                'description' => 'Link Button'
            ],
            'editor' => [
                'type' => Type::boolean(),
                'description' => 'Editor Text'
            ],
            'bold' => [
                'type' => Type::boolean(),
                'description' => 'Bold Title'
            ],
            'break' => [
                'type' => Type::boolean(),
                'description' => 'Break Line Title',
            ],
            'blank' => [
                'type' => Type::boolean(),
                'description' => 'New Window Button',
            ],
            'background' => [
                'type' => Type::boolean(),
                'description' => 'Background Media',
            ],
            'alt' => [
                'type' => Type::string(),
                'description' => 'Alt Media'
            ],
        ];
    }
}
