<?php

namespace App\GraphQL\Types\Enums;

use App\Enums\PageType;
use GraphQL\Type\Definition\EnumType;

class PageEnumType extends EnumType
{
    public function __construct()
    {
        $config = [
            'name' => 'PageEnum',
            'description' => 'Pages Types',
            'values' => PageType::attributes()
        ];

        parent::__construct($config);
    }

    public function toType(): EnumType
    {
        return $this;
    }
}
