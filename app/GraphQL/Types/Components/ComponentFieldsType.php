<?php

namespace App\GraphQL\Types\Components;

use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ComponentFieldsType extends GraphQLType
{
    protected $attributes = [
        'name' => 'ComponentFields',
        'description' => 'Component Fields'
    ];

    public function fields(): array
    {
        return [
            'fields' => [
                'type' => GraphQL::type('ComponentField'),
                'description' => 'Component Fields'
            ]
        ];
    }
}
