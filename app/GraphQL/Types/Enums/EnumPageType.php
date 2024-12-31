<?php

namespace App\GraphQL\Types\Enums;

use App\Enums\PagesType;
use GraphQL\Type\Definition\EnumType;

class EnumPageType extends EnumType
{
    public function __construct()
    {
        $config = [
            'name' => 'EnumPage',
            'description' => 'Pages Types',
            'values' => PagesType::attributes()
        ];

        parent::__construct($config);
    }

    public function toType(): EnumType
    {
        return $this;
    }
}
