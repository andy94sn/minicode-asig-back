<?php

namespace App\GraphQL\Types\Admins;

use App\Models\Admin;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AdminResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'AdminResponse',
        'description' => 'Response',
        'model' => Admin::class,
    ];

    public function fields(): array
    {
        return [
            'access_token' => [
                'type' => Type::string(),
                'description' => 'Access Token'
            ],
            'refresh_token' => [
                'type' => Type::string(),
                'description' => 'Refresh Token'
            ],
            'expires_at' => [
                'type' => Type::int(),
                'description' => 'Expiration'
            ],
            'admin' => [
                'type' => GraphQL::type('Admin'),
                'description' => 'Admin Data'
            ]
        ];
    }
}
