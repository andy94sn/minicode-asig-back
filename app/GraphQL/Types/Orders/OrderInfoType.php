<?php

namespace App\GraphQL\Types\Orders;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderInfoType extends GraphQLType
{
    protected $attributes = [
        'name' => 'OrderInfo',
        'description' => 'Order Info',
    ];

    public function fields(): array
    {
        return [
            'zone' => [
                'type' => Type::string(),
                'description' => 'Zone'
            ],
            'term' => [
                'type' => Type::string(),
                'description' => 'Term'
            ],
            'mode' => [
                'type' => Type::string(),
                'description' => 'Mode'
            ],
            'validity' => [
                'type' => Type::string(),
                'description' => 'Validity'
            ],
            'possession' => [
                'type' => Type::string(),
                'description' => 'Possession'
            ],
            'person_type' => [
                'type' => Type::string(),
                'description' => 'Person Type'
            ]
        ];
    }
}
