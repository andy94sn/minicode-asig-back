<?php

namespace App\GraphQL\Types\Enums;

use App\Enums\InsuranceType;
use GraphQL\Type\Definition\EnumType;

class InsuranceEnumType extends EnumType
{
    public function __construct()
    {
        $config = [
            'name' => 'InsuranceEnum',
            'description' => 'Component',
            'values' => InsuranceType::attributes()
        ];

        parent::__construct($config);
    }

    public function toType(): EnumType
    {
        return $this;
    }
}
