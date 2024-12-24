<?php

namespace App\GraphQL\Types\Components;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class TranslationInputType extends InputType
{
    protected $attributes = [
        'name' => 'TranslationInput',
        'description' => 'Translation Language'
    ];

    public function fields(): array
    {
        return [
            'lang' => [
                'type' => Type::string(),
                'description' => 'Language Translation'
            ],
            'value' => [
                'type' => Type::string(),
                'description' => 'Value Translation'
            ],
            'caption' => [
                'type' => Type::string(),
                'description' => 'Caption Translation'
            ],
            'alt' => [
                'type' => Type::string(),
                'description' => 'Alt Translation'
            ],
            'link' => [
                'type' => Type::string(),
                'description' => 'Link Translation'
            ],
            'answer' => [
                'type' => Type::string(),
                'description' => 'Answer Translation'
            ],
            'question' => [
                'type' => Type::string(),
                'description' => 'Question Translation'
            ],
            'background' => [
                'type' => Type::boolean(),
                'description' => 'Background Translation'
            ],
            'icon' => [
                'type' => Type::string(),
                'description' => 'Icon Translation'
            ],
            'label' => [
                'type' => Type::string(),
                'description' => 'Label Translation'
            ],
            'placeholder' => [
                'type' => Type::string(),
                'description' => 'Placeholder Translation'
            ],
            'type' => [
                'type' => Type::string(),
                'description' => 'Type Translation'
            ],
            'editor' => [
                'type' => Type::boolean(),
                'description' => 'Editor Translation'
            ],
            'bold' => [
                'type' => Type::boolean(),
                'description' => 'Bold Translation'
            ],
            'break' => [
                'type' => Type::boolean(),
                'description' => 'Break Line Translation'
            ],
            'blank' => [
                'type' => Type::boolean(),
                'description' => 'New Window Translation'
            ]
        ];
    }
}
