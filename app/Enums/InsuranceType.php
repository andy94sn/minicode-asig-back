<?php


namespace App\Enums;

enum InsuranceType: string
{
    case RCAI = 'rca';
    case RCAE = 'greenCard';

    /**
     * Return description
     */
    public function description(): string
    {
        return match($this) {
            self::RCAI  => 'rca',
            self::RCAE   => 'greenCard'
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
    public static function validate(string $insurance): bool
    {
        return in_array($insurance, self::values());
    }

}
