<?php

namespace App\GraphQL\Types\Enums;

use App\Enums\ComponentType;
use GraphQL\Type\Definition\EnumType;

class ComponentEnumType extends EnumType
{
    public function __construct()
    {
        $config = [
            'name' => 'ComponentEnum',
            'description' => 'Component',
            'values' => ComponentType::attributes()
        ];

        parent::__construct($config);
    }

    public function toType(): EnumType
    {
        return $this;
    }
}
