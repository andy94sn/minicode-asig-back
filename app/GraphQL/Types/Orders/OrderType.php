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
            'payment_status' => [
                'type' => Type::boolean(),
                'description' => 'Payment Link Status'
            ],
            'contract_number' => [
                'type' => Type::string(),
                'description' => 'Contract Number'
            ],
            'contract' => [
                'type' => Type::string(),
                'description' => 'Contract Number'
            ],
            'policy' => [
                'type' => Type::string(),
                'description' => 'Contract Number'
            ],
            'refund'  => [
                'type' => Type::float(),
                'description' => 'Refund'
            ],
            'info'  => [
                'type' => GraphQL::type('OrderInfo'),
                'description' => 'Order Info'
            ],
            'created_at'  => [
                'type' => Type::string(),
                'description' => 'Date'
            ],
            'transaction'  => [
                'type' => Type::string(),
                'description' => 'Transaction'
            ],
            'link' => [
                'type' => Type::string(),
                'description' => 'Policy link'
            ],
            'contract_link' => [
                'type' => Type::string(),
                'description' => 'Contract Link'
            ],
            'agent' => [
                'type' => Type::string(),
                'description' => 'Agent name'
            ],

            'code' => [
                'type' => Type::string(),
                'description' => 'Agent name'
            ],

            'certificate' => [
                'type' => Type::string(),
                'description' => 'Agent name'
            ],

            'vehicle_data' => [
                'type' => Type::string(),
                'description' => 'Vehicle data'
            ],
            'vehicle_insured' => [
                'type' => Type::string(),
                'description' => 'Vehicle insured'
            ],
            'vehicle_owner' => [
                'type' => Type::string(),
                'description' => 'Vehicle owner'
            ],
            'trailer_id' => [
                'type' => Type::string(),
                'description' => 'Trailer id'
            ],
            'payer_name' => [
                'type' => Type::string(),
                'description' => 'Payer name'
            ],
        ];
    }
}
