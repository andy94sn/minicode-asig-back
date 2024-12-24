<?php


namespace App\GraphQL\Types\Components;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class ComponentInputType extends InputType
{
    protected $attributes = [
        'name' => 'ComponentInput',
        'description' => 'Component Input'
    ];

    public function fields(): array
    {
        return [
            'token' => [
                'name' => 'token',
                'type' => Type::string(),
                'description' => 'Token Component'
            ]
        ];
    }
}
