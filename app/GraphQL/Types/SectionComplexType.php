<?php


namespace App\GraphQL\Types;

use App\Models\Section;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class SectionComplexType extends GraphQLType
{
    protected $attributes = [
        'name' => 'SectionComplex',
        'description' => 'Section Complex Page',
        'model' => Section::class
    ];

    public function fields(): array
    {
        return [
            'name' => [
                'type' => Type::string(),
                'description' => 'Name'
            ],
            'slug' => [
                'type' => Type::string(),
                'description' => 'Key'
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
                'type' => Type::listOf(GraphQL::type('ComponentComplex')),
                'description' => 'Component Section',
                'args' => [
                    'lang' => [
                        'name' => 'lang',
                        'type' => new nonNull(GraphQL::type('EnumLanguage')),
                        'description' => 'Language Page'
                    ]
                ],
                'resolve' => function ($root, $args) {
                    return $root->components;
                }
            ]
        ];
    }
}
