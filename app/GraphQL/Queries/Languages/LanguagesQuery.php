<?php

namespace App\GraphQL\Queries\Languages;

use App\Enums\LanguageType;
use Exception;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Query;

class LanguagesQuery extends Query
{
    protected $attributes = [
        'name' => 'getLanguages',
        'description' => 'Return Languages',
        'model' => LanguageType::class
    ];

    public function type(): Type
    {
        return Type::listOf(Type::string());
    }

    /**
     * @throws Error
     */
    public function resolve($root, $args)
    {
        try {
            return LanguageType::values();
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return [
                'success' => false,
                'message' => $exception->getMessage(),
                'data' => null,
            ];
        }
    }
}
