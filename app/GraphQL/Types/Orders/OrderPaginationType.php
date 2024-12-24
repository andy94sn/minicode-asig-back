<?php


namespace App\GraphQL\Types\Orders;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderPaginationType extends GraphQLType
{
    protected $attributes = [
        'name' => 'OrderPagination',
        'description' => 'Pagination with Orders'
    ];

    public function fields(): array
    {
        return [
            'data' => [
                'type' => Type::listOf(GraphQL::type('Order')),
                'description' => 'Orders List'
            ],
            'meta' => [
                'type' => GraphQL::type('Pagination'),
                'description' => 'Pagination Info'
            ],
        ];
    }
}
