<?php

namespace App\GraphQL\Types\Permissions;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class PermissionInputType extends InputType
{
    protected $attributes = [
        'name' => 'PermissionInput',
        'description' => ' Array Permissions',
    ];

    public function fields(): array
    {
        return [
            'token' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Token'
            ]
        ];
    }
}
