<?php

namespace App\GraphQL\Types\Rca;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class DocumentType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Document',
        'description' => 'Document Rca Data'
    ];

    public function fields(): array
    {
        return [
            'code' => [
                'name' => 'code',
                'type' => Type::string(),
                'description' => 'IDNP/IDNO Order Document'
            ],
            'certificate' => [
                'name' => 'certificate',
                'type' => Type::string(),
                'description' => 'Certificate Order Document'
            ],
            'trailer_id' => [
                'name' => 'trailer_id',
                'type' => Type::string(),
                'description' => 'Certificate Order Trailer'
            ],
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
                'description' => 'PersonName'
            ],
            'email' => [
                'name' => 'email',
                'type' => Type::string(),
                'description' => 'Email Order Document'
            ],
            'type' => [
                'name' => 'type',
                'type' => Type::string(),
                'description' => 'Type Rca Document'
            ],
            'zone' => [
                'name' => 'zone',
                'type' => Type::string(),
                'description' => 'Territory Insurance'
            ],
            'term' => [
                'name' => 'term',
                'type' => Type::string(),
                'description' => 'Terms Insurance'
            ],
            'validity' => [
                'name' => 'validity',
                'type' => Type::string(),
                'description' => 'Validity'
            ],
            'possession' => [
                'name' => 'possession',
                'type' => Type::string(),
                'description' => 'Possession Vehicle Right'
            ]
        ];
    }
}
