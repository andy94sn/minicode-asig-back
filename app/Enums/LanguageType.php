<?php


namespace App\Enums;

enum LanguageType: string
{
    case Romanian = 'ro';
    case Russian = 'ru';


    /**
     * Return description
     */
    public function description(): string
    {
        return match($this) {
            self::Romanian => 'ro',
            self::Russian => 'ru'
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
    public static function validate(string $language): bool
    {
        return in_array($language, self::values());
    }

    /**
     * Return default value
     */
    public static function default(): LanguageType
    {
        return self::Romanian;
    }
}
