<?php

namespace App\GraphQL\Types\Enums;

use App\Enums\GroupType;
use GraphQL\Type\Definition\EnumType;

class GroupEnumType extends EnumType
{
    public function __construct()
    {
        $config = [
            'name' => 'GroupEnum',
            'description' => 'Groups Data',
            'values' => GroupType::attributes()
        ];

        parent::__construct($config);
    }

    public function toType(): EnumType
    {
        return $this;
    }
}
