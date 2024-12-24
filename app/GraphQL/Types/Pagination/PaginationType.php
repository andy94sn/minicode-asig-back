<?php

namespace App\GraphQL\Types\Pagination;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PaginationType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Pagination',
        'description' => 'Pagination Type',
    ];

    public function fields(): array
    {
        return [
            'total' => [
                'type' => Type::int(),
                'description' => 'Total'
            ],
            'per_page' => [
                'type' => Type::int(),
                'description' => 'Number Items'
            ],
            'current_page' => [
                'type' => Type::int(),
                'description' => 'Current Page'
            ],
            'last_page' => [
                'type' => Type::int(),
                'description' => 'Last Page'
            ],
            'from' => [
                'type' => Type::int(),
                'description' => 'First Item Page'
            ],
            'to' => [
                'type' => Type::int(),
                'description' => 'Last Item Page'
            ]
        ];
    }
}
