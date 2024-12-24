<?php

namespace App\GraphQL\Types\Settings;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class ValueInputType extends InputType
{
    protected $attributes = [
        'name' => 'ValueInput',
        'description' => 'Values Data'
    ];

    public function fields(): array
    {
        return [
            'lang' => [
                'type' => Type::string(),
                'description' => 'Language'
            ],
            'value' => [
                'type' => Type::string(),
                'description' => 'Value'
            ]
        ];
    }
}
