<?php

namespace App\GraphQL\Types\Components;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UploadResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'UploadResponse',
        'description' => 'Response Upload',
    ];

    public function fields(): array
    {
        return [
            'path' => [
                'type' => Type::string(),
                'description' => 'Path',
            ]
        ];
    }
}
