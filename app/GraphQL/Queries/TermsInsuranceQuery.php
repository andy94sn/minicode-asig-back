<?php

namespace App\GraphQL\Queries;

use App\Services\HelperService;
use App\Services\TermService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Exception;

class TermsInsuranceQuery extends Mutation
{
    protected $attributes = [
        'name' => 'getTerms',
        'description' => 'Return Terms Insurances (Returnează perioadele de asigurare a autovehiculului)'
    ];


    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Term'));
    }

    public function args(): array
    {
        return [
            'lang' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Language',
            ]
        ];
    }

    public function resolve($root, array $args)
    {
        $lang = $args['lang'];

        try {
            $service = new TermService($args['lang']);
            return $service->terms();
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
