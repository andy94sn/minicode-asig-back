<?php

namespace App\GraphQL\Types\Contacts;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ContactResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'ContactResponse',
        'description' => 'Contact Response Type',
    ];

    public function fields(): array
    {
        return [
            'status' => [
                'name' => 'status',
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'Status'
            ]
        ];
    }
}
