<?php

namespace App\GraphQL\Types\Errors;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ErrorType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Error',
        'description' => 'Return Error'
    ];

    public function fields(): array
    {
        return [
            'code' => [
                'type' => Type::string(),
                'description' => 'Code'
            ],
            'message' => [
                'type' => Type::string(),
                'description' => 'Message'
            ]
        ];
    }
}
