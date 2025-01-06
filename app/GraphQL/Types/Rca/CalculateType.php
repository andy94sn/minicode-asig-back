<?php

namespace App\GraphQL\Types\Rca;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CalculateType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Calculate',
        'description' => 'Calculate Type',
    ];

    public function fields(): array
    {
        return [
            'primeSumMdl' => [
                'type' => Type::float(),
                'description' => 'Prime Sum MDL',
            ],
            'primeSumEuro' => [
                'type' => Type::float(),
                'description' => 'Prime Sum EURO'
            ],
            'exchangeRate' => [
                'type' => Type::float(),
                'description' => 'Exchange Rate',
            ],
            'personName' => [
                'type' => Type::string(),
                'description' => 'Person Name',
            ],
            'vehicleCategory' => [
                'type' => Type::string(),
                'description' => 'Vehicle Category',
            ],
            'bonusMalusClass' => [
                'type' => Type::int(),
                'description' => 'Bonus Malus Class',
            ],
            'firstName' => [
                'type' => Type::string(),
                'description' => 'First Name',
            ],
            'lastName' => [
                'type' => Type::string(),
                'description' => 'Last Name',
            ],
            'vehicleMark' => [
                'type' => Type::string(),
                'description' => 'Vehicle Mark',
            ],
            'vehicleModel' => [
                'type' => Type::string(),
                'description' => 'Vehicle Model',
            ],
            'vehicleRegistrationNumber' => [
                'type' => Type::string(),
                'description' => 'Vehicle Registration Number',
            ],
            'expirationLastContract' => [
                'type' => Type::string(),
                'description' => 'Expiration Date'
            ],
            'signature' => [
                'type' => Type::string(),
                'description' => 'Signature Data'
            ]
        ];
    }
}
