<?php


namespace App\GraphQL\Types\Contacts;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ContactPaginationType extends GraphQLType
{
    protected $attributes = [
        'name' => 'ContactPagination',
        'description' => 'Pagination with Contacts'
    ];

    public function fields(): array
    {
        return [
            'data' => [
                'type' => Type::listOf(GraphQL::type('Contact')),
                'description' => 'Contacts List'
            ],
            'meta' => [
                'type' => GraphQL::type('Pagination'),
                'description' => 'Pagination Info'
            ],
        ];
    }
}
