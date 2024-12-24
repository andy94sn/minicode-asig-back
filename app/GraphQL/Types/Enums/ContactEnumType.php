<?php

namespace App\GraphQL\Types\Enums;
use App\Enums\ContactType;
use GraphQL\Type\Definition\EnumType;

class ContactEnumType extends EnumType
{
    public function __construct()
    {
        $config = [
            'name' => 'ContactEnum',
            'description' => 'Contacts Enum',
            'values' => ContactType::attributes()
        ];

        parent::__construct($config);
    }

    public function toType(): EnumType
    {
        return $this;
    }
}
