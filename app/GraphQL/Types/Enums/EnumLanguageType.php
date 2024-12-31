<?php

namespace App\GraphQL\Types\Enums;

use App\Enums\LanguageType;
use GraphQL\Type\Definition\EnumType;

class EnumLanguageType extends EnumType
{
    public function __construct()
    {
        $config = [
            'name' => 'EnumLanguage',
            'description' => 'Languages Application',
            'values' => LanguageType::attributes()
        ];

        parent::__construct($config);
    }

    public function toType(): EnumType
    {
        return $this;
    }
}
