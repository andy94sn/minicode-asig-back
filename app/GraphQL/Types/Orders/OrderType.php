<?php

namespace App\GraphQL\Types\Orders;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Order',
        'description' => 'Order Type',
    ];

    public function fields(): array
    {
        return [
            'token' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Token'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'Name'
            ],
            'type' => [
                'type' => Type::string(),
                'description' => 'Type'
            ],
            'price'  => [
                'type' => Type::float(),
                'description' => 'Price'
            ],
            'email' => [
                'type' => Type::string(),
                'description' => 'Email'
            ],
            'phone' => [
                'type' => Type::string(),
                'description' => 'Phone'
            ],
            'status' => [
                'type' => Type::string(),
                'description' => 'Status'
            ],
            'contract' => [
                'type' => Type::string(),
                'description' => 'Contract Number'
            ],
            'refund'  => [
                'type' => Type::int(),
                'description' => 'Refund'
            ],
            'info'  => [
                'type' => GraphQL::type('OrderInfo'),
                'description' => 'Order Info'
            ],
            'created_at'  => [
                'type' => Type::string(),
                'description' => 'Date'
            ]
        ];
    }
}
