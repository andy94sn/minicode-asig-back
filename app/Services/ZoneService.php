<?php

namespace App\Services;
use GraphQL\Error\Error;

class ZoneService
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
            'Z3' => ['ro' => 'Țările din sistemul “Carte Verde” (Europa)', 'ru' => 'Все страны << Зеленой Карты >> (Европа)'],
            'Z1' => ['ro' => 'Ucraina', 'ru' => 'Украина']
        ];

        if (!array_key_exists($this->lang, current($data))) {
            throw new Error("Language '{$this->lang}' is not supported.");
        }

        return array_map(function ($key, $value) {
            $icon = '';
            if ($key == 'Z1') {
                $icon = 'ukraine';
            } elseif ($key == 'Z3') {
                $icon = 'europe';
            }
            return [
                'key' => $key,
                'value' => $value[$this->lang],
                'icon' => $icon
            ];
        }, array_keys($data), $data);
    }
}
