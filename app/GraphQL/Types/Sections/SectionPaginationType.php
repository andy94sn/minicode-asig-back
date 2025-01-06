<?php

namespace App\GraphQL\Types\Sections;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class SectionPaginationType extends GraphQLType
{
    protected $attributes = [
        'name' => 'SectionPagination',
        'description' => 'Pagination with Sections'
    ];

    public function fields(): array
    {
        return [
            'data' => [
                'type' => Type::listOf(GraphQL::type('Section')),
                'description' => 'Sections List'
            ],
            'meta' => [
                'type' => GraphQL::type('Pagination'),
                'description' => 'Pagination Info'
            ],
        ];
    }
}
