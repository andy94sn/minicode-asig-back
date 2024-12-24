<?php

namespace App\Enums;

enum DocumentType: string
{
    case Demand = 'demand';
    case Contract = 'contract';
    case InsurancePolicy = 'policy';

    /**
     * Return description
     */
    public function description(): string
    {
        return match($this) {
            self::Demand => 'Demand',
            self::Contract => 'Contract',
            self::InsurancePolicy => 'InsurancePolicy'
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
     * Return values
     */
    public static function getValues(): array
    {
        $values = [];
        $descriptions = self::descriptions();


        foreach (self::values() as $index => $value) {
            $values[$value] = $descriptions[$index];
        }

        return $values;
    }

    /**
     * Return type document
     */
    public static function getType($type): ?string
    {
        $descriptions = self::descriptions();
        $values = self::values();

        $index = array_search($type, $descriptions);

        return $values[$index] ?? null;
    }

}
