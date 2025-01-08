<?php


namespace App\Enums;

enum PagesType: string
{
    case HOME      = 'home';
    case RCAI      = 'rcai';
    case RCAE      = 'rcae';
    case SUCCESS   = 'success';
    case CONTACTS  = 'contacts';
    case HEADER    = 'header';
    case FOOTER    = 'footer';
    case TERMS     = 'terms';

    /**
     * Return description
     */
    public function description(): string
    {
        return match($this) {
            self::HOME      => 'home',
            self::RCAI      => 'rcai',
            self::RCAE      => 'rcae',
            self::SUCCESS   => 'success',
            self::CONTACTS  => 'contacts',
            self::HEADER    => 'header',
            self::FOOTER    => 'footer',
            self::TERMS     => 'terms'
        };
    }

    /**
     * Values
     */
    public static function values(): array
    {
        return array_map(fn(self $lang) => $lang->value, self::cases());
    }

    /**
     * Descriptions
     */
    public static function descriptions(): array
    {
        return array_map(fn($case) => $case->description(), self::cases());
    }

    /**
     * Return attributes GraphQL EnumType
     */
    public static function attributes(): array
    {
        $values = [];
        $descriptions = self::descriptions();


        foreach (self::values() as $index => $value) {
            $values[$value] = $descriptions[$index];
        }

        return $values;
    }

    /**
     * Validate value
     */
    public static function validate(string $contact): bool
    {
        return in_array($contact, self::values());
    }

    /**
     * Validate value
     */
    public static function default(): PagesType
    {
        return self::HOME;
    }

}
