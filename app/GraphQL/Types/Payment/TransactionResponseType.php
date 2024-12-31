<?php

namespace App\GraphQL\Types\Payment;

use Rebing\GraphQL\Support\Type as GraphQLType;
use GraphQL\Type\Definition\Type;

class TransactionResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'TransactionResponse',
        'description' => 'Transaction Response Payment'
    ];

    public function fields(): array
    {
        return [
            'transaction' => [
                'type' => Type::string(),
                'description' => 'Transaction URL'
            ]
        ];
    }
}
