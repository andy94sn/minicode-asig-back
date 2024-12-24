<?php


namespace App\Enums;

enum TranslationType: string
{
    case Text = 'text';
    case Link = 'link';
    case Image = 'image';
    case Video = 'video';
    case List  = 'list';
    case Button  = 'button';
    case Icon  = 'icon';
    case Input  = 'input';


    /**
     * Return description
     */
    public function description(): string
    {
        return match($this) {
            self::Text => 'text',
            self::Link => 'link',
            self::Image => 'image',
            self::Video => 'video',
            self::List => 'list',
            self::Button => 'button',
            self::Icon => 'icon',
            self::Input => 'input'
        };
    }

    /**
     * Values
     */
    public static function values(): array
    {
        return array_map(fn(self $type) => $type->value, self::cases());
    }

    /**
     * Descriptions
     */
    public static function descriptions(): array
    {
        return array_map(fn(self $type) => $type->description(), self::cases());
    }

    /**
     * Return attributes GraphQL EnumType
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
}
