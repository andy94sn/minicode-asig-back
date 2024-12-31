<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PersonType extends GraphQLType
{
    protected $attributes = [
        'name' => 'PersonType',
        'description' => 'Person Types',
    ];

    public function fields(): array
    {
        return [
            'key' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Key',
            ],
            'value' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Value',
            ],
        ];
    }
}
