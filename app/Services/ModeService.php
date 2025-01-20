<?php

namespace App\Services;

use GraphQL\Error\Error;

class ModeService
{
    protected string $lang;

    public function __construct($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @throws Error
     */
    public function zones(): array
    {
        $data =  [
            'Usual'     => ['ro' => 'STANDARD', 'ru' => 'ОБЫЧНЫЙ']
//            'Minibus'   => ['ro' => 'MICROBUZ', 'ru' => 'АВТОБУС'],
//            'Intercity' => ['ro' => 'INTERURBAN', 'ru' => 'МЕЖДУНАРОДНЫЙ'],
//            'Taxi'      => ['ro' => 'TAXI', 'ru' => 'ТАКСИ'],
//            'Rentcar'   => ['ro' => 'ÎNCHIRIERE', 'ru' => 'АРЕНДА']
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
