<?php


namespace App\GraphQL\Types\Settings;

use App\Models\Setting;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class SettingType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Setting',
        'description' => 'Settings Data',
        'model' => Setting::class
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
            'value' => [
                'type' => Type::listOf(GraphQL::type('ValueResponse')),
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
