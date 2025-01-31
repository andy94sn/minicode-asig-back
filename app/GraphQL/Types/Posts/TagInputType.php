<?php

namespace App\GraphQL\Types\Posts;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class TagInputType extends InputType
{
    protected $attributes = [
        'name' => 'TagInput',
        'description' => 'Tag Input Type'
    ];

    public function fields(): array
    {
        return [
            'key' => [
                'type' => Type::int(),
                'description' => 'Key'
            ],
            'value' => [
                'type' => Type::string(),
                'description' => 'Value'
            ]
        ];
    }
}
