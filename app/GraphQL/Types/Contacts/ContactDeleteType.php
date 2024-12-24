<?php


namespace App\GraphQL\Types\Contacts;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ContactDeleteType extends GraphQLType
{
    protected $attributes = [
        'name' => 'ContactDelete',
        'description' => 'Response Contact Delete'
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
