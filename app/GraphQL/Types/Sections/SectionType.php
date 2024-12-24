<?php


namespace App\GraphQL\Types\Sections;

use App\Models\Section;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class SectionType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Section',
        'description' => 'Section Page',
        'model' => Section::class
    ];

    public function fields(): array
    {
        return [
            'token' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Token'
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Name'
            ],
            'slug' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'SEO URL'
            ],
            'order' => [
                'type' => Type::int(),
                'description' => 'Order'
            ],
            'status' => [
                'type' => Type::boolean(),
                'description' => 'Status'
            ],
            'components' => [
                'type' => Type::listOf(GraphQL::type('Component')),
                'description' => 'Component Section'
            ]
        ];
    }
}
