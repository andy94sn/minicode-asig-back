<?php

namespace App\GraphQL\Types\Permissions;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use App\Models\Permission;

class PermissionType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Permission',
        'description' => 'Permission',
        'model' => Permission::class,
    ];

    public function fields(): array
    {
        return [
            'token' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Token'
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Name'
            ]
        ];
    }
}
