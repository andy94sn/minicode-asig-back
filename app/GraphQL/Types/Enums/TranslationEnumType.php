<?php

namespace App\GraphQL\Types\Enums;

use App\Enums\TranslationType;
use GraphQL\Type\Definition\EnumType;

class TranslationEnumType extends EnumType
{
    public function __construct()
    {
        $config = [
            'name' => 'TranslationEnum',
            'description' => 'Translations Application',
            'values' => TranslationType::attributes()
        ];

        parent::__construct($config);
    }

    public function toType(): EnumType
    {
        return $this;
    }
}
