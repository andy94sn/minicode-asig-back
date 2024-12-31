<?php

namespace App\GraphQL\Types\Payment;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TransactionType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Transaction',
        'description' => 'Transaction Type Payment',
    ];

    public function fields(): array
    {
        return[
            'status' => [
                'name' => 'status',
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'Status',
            ],
            'message' => [
                'name' => 'message',
                'type' => Type::string(),
                'description' => 'Message',
            ],
            'result' => [
                'name' => 'result',
                'type' => GraphQL::type('TransactionResponse'),
                'description' => 'Result'
            ]
        ];
    }
}
