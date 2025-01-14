<?php


namespace App\Enums;

enum ComponentType: string
{
    case TITLE  = 'title';
    case TEXT   = 'text';
    case BUTTON = 'button';
    case MEDIA  = 'media';
    case FAQ    = 'faq';
    case CARD   = 'card';
    case FORM   = 'form';
    case INPUT  = 'input';
    case SELECT  = 'select';


    /**
     * Return description
     */
    public function description(): string
    {
        return match($this) {
            self::TITLE  => 'title',
            self::TEXT   => 'text',
            self::BUTTON => 'button',
            self::MEDIA  => 'media',
            self::FAQ    => 'faq',
            self::CARD   => 'card',
            self::FORM   => 'form',
            self::INPUT  => 'input',
            self::SELECT  => 'select'
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
    public static function validate(string $component): bool
    {
        return in_array($component, self::values());
    }

    /**
     * Validate value
     */
    public static function default(): ComponentType
    {
        return self::TEXT;
    }

}
