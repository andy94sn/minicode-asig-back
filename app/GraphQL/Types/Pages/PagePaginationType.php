<?php

namespace App\GraphQL\Types\Pages;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PagePaginationType extends GraphQLType
{
    protected $attributes = [
        'name' => 'PagePagination',
        'description' => 'Pagination with Pages'
    ];

    public function fields(): array
    {
        return [
            'data' => [
                'type' => Type::listOf(GraphQL::type('Page')),
                'description' => 'Pages List'
            ],
            'meta' => [
                'type' => GraphQL::type('Pagination'),
                'description' => 'Pagination Info'
            ],
        ];
    }
}
