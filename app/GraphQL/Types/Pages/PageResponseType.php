<?php

namespace App\GraphQL\Types\Pages;

use App\Models\Page;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PageResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'PageResponse',
        'description' => 'Response',
        'model' => Page::class
    ];

    public function fields(): array
    {
        return[
            'status' => [
                'type' => Type::boolean(),
                'description' => 'Status',
            ],
            'message' => [
                'type' => Type::string(),
                'description' => 'Message'
            ],
            'page' => [
                'type' => GraphQL::type('Page'),
                'description' => 'Page',
            ]
        ];
    }
}
