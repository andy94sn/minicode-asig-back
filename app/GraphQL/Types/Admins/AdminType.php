<?php

namespace App\GraphQL\Types\Admins;

use App\Models\Admin;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AdminType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Admin',
        'description' => 'Admin',
        'model' => Admin::class,
    ];

    public function fields(): array
    {
        return [
            'token' => [
                'type' =>  Type::nonNull(Type::string()),
                'description' => 'Token'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'Name',
            ],
            'email' => [
                'type' => Type::string(),
                'description' => 'Email',
            ],
            'status' => [
                'type' => Type::boolean(),
                'description' => 'Status',
            ],
            'role' => [
                'type' => Type::string(),
                'description' => 'Role'
            ],
            'permissions' => [
                'type' => Type::listOf(Type::string()),
                'description' => 'Permissions'
            ]
        ];
    }
}
