<?php

namespace App\GraphQL\Types\Components;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class FieldType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Field',
        'description' => 'Field Description'
    ];

    public function fields(): array
    {
        return [
            'label' => [
                'type' => Type::string(),
                'description' => 'Label Field'
            ],
            'type' => [
                'type' => Type::string(),
                'description' => 'Type Field'
            ],
            'required' => [
                'type' => Type::boolean(),
                'description' => 'Required type'
            ],
        ];
    }
}
