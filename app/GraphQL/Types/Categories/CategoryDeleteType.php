<?php

namespace App\GraphQL\Types\Categories;

use App\Models\Category;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CategoryDeleteType extends GraphQLType
{
    protected $attributes = [
        'name' => 'CategoryDelete',
        'description' => 'Response Category Delete',
        'model' => Category::class,
    ];

    public function fields(): array
    {
        return [
            'status' => [
                'name' => 'status',
                'type' => Type::boolean(),
                'description' => 'Status'
            ]
        ];
    }
}
