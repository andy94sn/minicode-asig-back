<?php


namespace App\Enums;

enum ContactType: string
{
    case HOME = 'home';
    case CONTACTS  = 'contacts';
    case BLOG   = 'blog';
    case ABOUT  = 'about';
    case FAQ    = 'faq';

    /**
     * Return description
     */
    public function description(): string
    {
        return match($this) {
            self::HOME  => 'home',
            self::CONTACTS   => 'contacts',
            self::BLOG => 'blog',
            self::ABOUT  => 'about',
            self::FAQ    => 'faq'
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
    public static function default(): ContactType
    {
        return self::CONTACTS;
    }

}
