<?php


namespace App\GraphQL\Types\Contacts;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ContactType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Contact',
        'description' => 'Contact Description'
    ];

    public function fields(): array
    {
        return [
            'token' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Token'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'Name'
            ],
            'phone' => [
                'type' => Type::string(),
                'description' => 'Phone'
            ],
            'email' => [
                'type' => Type::string(),
                'description' => 'Email'
            ],
            'page' => [
                'type' => GraphQL::type('ContactEnum'),
                'description' => 'Page Type'
            ],
            'message' => [
                'type' => Type::string(),
                'description' => 'Message'
            ]
        ];
    }
}
