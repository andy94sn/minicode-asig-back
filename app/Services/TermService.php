<?php

namespace App\Services;


use GraphQL\Error\Error;

class TermService
{
    protected string $lang;

    public function __construct($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @throws Error
     */
    public function terms(): array
    {
        $data =  [
            'd15' => ['ro' => '15 zile', 'ru' => '15 дней'],
            'm1'  => ['ro' => '1 lună',  'ru' => '1 месяц'],
            'm2'  => ['ro' => '2 luni',  'ru' => '2 месяца'],
            'm3'  => ['ro' => '3 luni',  'ru' => '3 месяца'],
            'm4'  => ['ro' => '4 luni',  'ru' => '4 месяца'],
            'm5'  => ['ro' => '5 luni',  'ru' => '5 месяцев'],
            'm6'  => ['ro' => '6 luni',  'ru' => '6 месяцев'],
            'm7'  => ['ro' => '7 luni',  'ru' => '7 месяцев'],
            'm8'  => ['ro' => '8 luni',  'ru' => '8 месяцев'],
            'm9'  => ['ro' => '9 luni',  'ru' => '9 месяцев'],
            'm10' => ['ro' => '10 luni', 'ru' => '10 месяцев'],
            'm11' => ['ro' => '11 luni', 'ru' => '11 месяцев'],
            'm12' => ['ro' => '12 luni', 'ru' => '12 месяцев']
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
