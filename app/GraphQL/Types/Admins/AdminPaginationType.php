<?php


namespace App\GraphQL\Types\Admins;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AdminPaginationType extends GraphQLType
{
    protected $attributes = [
        'name' => 'AdminPagination',
        'description' => 'Pagination Admins'
    ];

    public function fields(): array
    {
        return [
            'data' => [
                'type' => Type::listOf(GraphQL::type('Admin')),
                'description' => 'Admins List'
            ],
            'meta' => [
                'type' => GraphQL::type('Pagination'),
                'description' => 'Pagination Info'
            ],
        ];
    }
}
