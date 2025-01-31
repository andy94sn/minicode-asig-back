<?php

namespace App\GraphQL\Types\Posts;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TagType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Tag',
        'description' => 'Tag Type'
    ];

    public function fields(): array
    {
        return [
            'key' => [
                'type' => Type::int(),
                'description' => 'Key',
            ],
            'value' => [
                'type' => Type::string(),
                'description' => 'Value'
            ]
        ];
    }
}
