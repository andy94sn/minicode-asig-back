<?php


namespace App\Enums;

enum StatusType: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case REFUND  = 'cancel';
    case TIMEOUT  = 'expired';
    case FAILED   = 'failed';

    /**
     * Return description
     */
    public function description(): string
    {
        return match($this) {
            self::PENDING  => 'pending',
            self::COMPLETED   => 'completed',
            self::REFUND => 'cancel',
            self::TIMEOUT  => 'expired',
            self::FAILED    => 'failed'
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
    public static function default(): StatusType
    {
        return self::PENDING;
    }

}
