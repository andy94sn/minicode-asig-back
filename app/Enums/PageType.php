<?php


namespace App\Enums;

enum PageType: string
{
    case SIMPLE = 'simple';
    case COMPLEX  = 'complex';
    case GENERAL  = 'general';

    /**
     * Return description
     */
    public function description(): string
    {
        return match($this) {
            self::SIMPLE  => 'simple',
            self::COMPLEX => 'complex',
            self::GENERAL => 'general'
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
    public static function default(): PageType
    {
        return self::SIMPLE;
    }

}
