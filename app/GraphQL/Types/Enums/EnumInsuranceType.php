<?php

namespace App\GraphQL\Types\Enums;

use App\Enums\InsuranceType;
use GraphQL\Type\Definition\EnumType;

class EnumInsuranceType extends EnumType
{
    public function __construct()
    {
        $config = [
            'name' => 'EnumInsurance',
            'description' => 'Insurances Types',
            'values' => InsuranceType::attributes()
        ];

        parent::__construct($config);
    }

    public function toType(): EnumType
    {
        return $this;
    }
}
