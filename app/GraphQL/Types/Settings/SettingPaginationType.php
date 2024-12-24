<?php


namespace App\GraphQL\Types\Settings;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class SettingPaginationType extends GraphQLType
{
    protected $attributes = [
        'name' => 'SettingPagination',
        'description' => 'Pagination with Settings'
    ];

    public function fields(): array
    {
        return [
            'data' => [
                'type' => Type::listOf(GraphQL::type('Setting')),
                'description' => 'Orders List'
            ],
            'meta' => [
                'type' => GraphQL::type('Pagination'),
                'description' => 'Pagination Info'
            ],
        ];
    }
}
