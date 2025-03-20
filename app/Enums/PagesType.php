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
    case CONSUMER = 'consumer';

    case ERROR    = 'error';

    case NOT_FOUND = 'not_found';
    case PRIVACY   = 'politica_de_confidenialitate';
    case ORDER_LINK = 'order_link';

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
            self::TERMS     => 'terms',
            self::CONSUMER  => 'consumer',
            self::ERROR     => 'error',
            self::NOT_FOUND => 'not_found',
            self::PRIVACY   => 'politica_de_confidenialitate',
            self::ORDER_LINK => 'order_link'
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
