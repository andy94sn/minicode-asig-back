<?php

namespace App\Services;
use GraphQL\Error\Error;

class PersonsService
{
    protected string $lang;

    public function __construct($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @throws Error
     */
    public function types(): array
    {
        $data =  [
            1 => ['ro' => 'Persoana fizică',   'ru' => 'Физическое Лицо'],
            2 => ['ro' => 'Persoana juridică', 'ru' => 'Юридическое Лицо']
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
