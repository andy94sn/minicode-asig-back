<?php

namespace App\GraphQL\Types\Admins;

use App\Models\Admin;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AdminDeleteType extends GraphQLType
{
    protected $attributes = [
        'name' => 'AdminDelete',
        'description' => 'Response Admin Delete',
        'model' => Admin::class,
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
