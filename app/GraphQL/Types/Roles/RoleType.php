<?php


namespace App\GraphQL\Types\Roles;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;
use App\Models\Role;

class RoleType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Role',
        'description' => 'Role',
        'model' => Role::class,
    ];

    public function fields(): array
    {
        return [
            'token' => [
                'name' => 'token',
                'type' =>  Type::string(),
                'description' => 'Token'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'Name Role'
            ],
            'description' => [
                'name' => 'description',
                'type' => Type::string(),
                'description' => 'Description Role'
            ],
            'permissions' => [
                'type' => Type::listOf(GraphQL::type('Permission')),
                'description' => 'List Permissions'
            ]
        ];
    }
}

