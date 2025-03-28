<?php

namespace App\Enums;

enum GroupType: string
{
    case GENERAL = 'general';
    // case SOCIAL = 'social';
    // case CONTACT = 'contact';
    // case PAYMENT = 'payment';
    // case MAIL    = 'mail';
    // case ADMIN   = 'admin';

    /**
     * Return description
     */
    public function description(): string
    {
        return match($this) {
            self::GENERAL => 'general',
            // self::SOCIAL  => 'social',
            // self::CONTACT => 'contact',
            // self::PAYMENT => 'payment',
            // self::MAIL    => 'mail',
            // self::ADMIN    => 'admin',
        };
    }

    /**
     * Values
     */
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    /**
     * Descriptions
     */
    public static function descriptions(): array
    {
        return array_map(fn($case) => $case->description(), self::cases());
    }

    /**
     * Return attributes GraphQL GroupType
     */
    public static function attributes(): array
    {
        $values = [];
        $descriptions = self::descriptions();

        foreach (self::values() as $index => $value) {
            $values[$descriptions[$index]] = $value;
        }

        return $values;
    }

    /**
     * Validate value
     */
    public static function validate(string $value): bool
    {
        return in_array($value, self::values());
    }

    /**
     * Validate value
     */
    public static function default(): GroupType
    {
        return self::GENERAL;
    }

}
