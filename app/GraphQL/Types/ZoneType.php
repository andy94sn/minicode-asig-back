<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ZoneType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Zone',
        'description' => 'Zone Type',
    ];

    public function fields(): array
    {
        return [
            'key' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Key',
            ],
            'value' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Value',
            ],
            'icon' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Icon',
            ],
        ];
    }
}
