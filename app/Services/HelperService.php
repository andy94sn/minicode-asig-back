<?php

namespace App\Services;

use Illuminate\Support\Str;
use Stevebauman\Purify\Facades\Purify;

class HelperService
{
    /**
     * Create slug.
     *
     * @param string $text
     * @return string
     */
    public static function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
        $text = trim($text, '-');
        return preg_replace('~[^-\w]+~', '', $text);
    }

    /**
     * Cleaning String
     *
     * @param string $string
     * @return string
     */

    public static function clean(string $string): string
    {
        $string = Purify::clean($string);

        return Str::of($string)
            ->trim()
            ->replace('  ', ' ')
            ->stripTags()
            ->toString();
    }

    /**
     * Error Message.
     *
     * @param $language
     * @param string|null $error
     * @return string
     */
    public static function message($language, string $error = null): array|string
    {
        $messages = [
            'ro' => [
                'invalid'  => 'Date invalide',
                'error'    => 'Ceva nu a mers bine',
                'role'     => 'Nu se poate șterge rolul, este atribuit',
                'denied'   => 'Autorizare interzisă',
                'failed'   => 'Autorizare eșuată',
                'exists'   => 'Există deja',
                'permission' => 'Nu aveți permisiune',
                'found'    => 'Nu a fost găsit',
                'password_mismatch'    => 'Parolele nu se potrivesc',
            ],
            'en' => [
                'invalid'  => 'Invalid data',
                'error'    => 'Something went wrong',
                'role'     => 'Cannot delete role, it is assigned',
                'denied'   => 'Authorization denied',
                'failed'   => 'Authorization failed',
                'exists'   => 'It already exists - ',
                'permission' => 'No permission',
                'found'     => 'Not found -',
                'password_mismatch'  => 'The passwords do not match',
            ],
            'ru' => [
                'invalid'  => 'Неверные данные',
                'error'    => 'Что-то пошло не так',
                'role'     => 'Невозможно удалить роль, она назначена',
                'denied'   => 'Авторизация отклонена',
                'failed'   => 'Авторизация не удалась',
                'exists'   => 'Уже существует - ',
                'permission' => 'Нет разрешения',
                'found'     => 'Не найдено -',
                'password_mismatch'  => 'Пароли не совпадают',
            ]
        ];

        if (!array_key_exists($language, $messages)) {
            $language = 'ro';
        }

        if ($error === null) {
            return $messages[$language];
        }

        return $messages[$language][$error] ?? $messages['ro'][$error];
    }
}
