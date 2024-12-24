<?php

namespace App\GraphQL\Types\Pages;

use App\Models\Page;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PageDeleteType extends GraphQLType
{
    protected $attributes = [
        'name' => 'PageDelete',
        'description' => 'Response Page Delete',
        'model' => Page::class
    ];

    public function fields(): array
    {
        return [
            'status' => [
                'name' => 'status',
                'type' => Type::boolean(),
                'description' => 'Status',
            ]
        ];
    }
}
