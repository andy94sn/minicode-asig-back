<?php

namespace App\GraphQL\Queries\Errors;

use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class ErrorsQuery extends Query
{
    protected $attributes = [
        'name' => 'getErrors',
        'description' => 'Return Errors'
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Error'));
    }

    public function args(): array
    {
        return [
            'lang' => [
                'name' => 'lang',
                'type' => GraphQL::type('LanguageEnum'),
                'description' => 'Language',
            ]
        ];
    }

    public function resolve($root, $args)
    {
        $lang = $args['lang'] ?? 'ro';

        try{
            $errors = HelperService::message($lang) ?? [];

            return array_map(function ($code, $message) {
                return [
                    'code' => $code,
                    'message' => $message,
                ];
            }, array_keys($errors), $errors);

        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
