<?php

namespace App\GraphQL\Types\Orders;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'OrderResponse',
        'description' => 'Order Response Type',
    ];

    public function fields(): array
    {
        return [
            'token' => [
                'name' => 'token',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Token',
            ]
        ];
    }
}
