<?php

namespace App\Services;
use GraphQL\Error\Error;

class PossessionService
{
    protected string $lang;

    public function __construct($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @throws Error
     */
    public function possessions(): array
    {
        $data =  [
            'Property'        => ['ro' => 'Proprietate', 'ru' => 'Владелец'],
            'Leasing'         => ['ro' => 'Leasing', 'ru' => 'Лизинг'],
            'Lease'           => ['ro' => 'Comodat', 'ru' => 'Аренда'],
            'PowerOfAttorney' => ['ro' => 'Procură', 'ru' => 'Доверенность']
        ];

        if (!array_key_exists($this->lang, current($data))) {
            throw new Error("Language '{$this->lang}' is not supported.");
        }

        return array_map(function ($key, $value) {
            return [
                'key' => $key,
                'value' => $value[$this->lang],
            ];
        }, array_keys($data), $data);
    }
}
