<?php

namespace App\GraphQL\Types\Settings;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\InputType;

class SettingInputType extends InputType
{
    protected $attributes = [
        'name' => 'SettingInput',
        'description' => 'Setting Input Data'
    ];

    public function fields(): array
    {
        return [
            'token' => [
                'type' => Type::string(),
                'description' => 'Token'
            ],
            'key' => [
                'type' => Type::string(),
                'description' => 'Key'
            ],
            'group' => [
                'type' => Type::string(),
                'description' => 'Group'
            ],
            'values' => [
                'type' => Type::listOf(GraphQL::type('ValueInput')),
                'description' => 'Values Response'
            ],
            'description' => [
                'type' => Type::string(),
                'description' => 'Description'
            ],
            'status' => [
                'type' => Type::boolean(),
                'description' => 'Status'
            ]
        ];
    }
}
