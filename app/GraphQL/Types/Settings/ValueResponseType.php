<?php

namespace App\GraphQL\Types\Settings;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ValueResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'ValueResponse',
        'description' => 'Values Response'
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
            ]
        ];
    }
}
