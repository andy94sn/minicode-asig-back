<?php

namespace App\GraphQL\Queries;

use App\Services\TermService;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Exception;

class TermsInsuranceQuery extends Mutation
{
    protected $attributes = [
        'name' => 'getTerms',
        'description' => 'Return Terms Insurances'
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

    public function resolve($root, array $args): array
    {
        try {
            $service = new TermService($args['lang']);
            return $service->terms();
        } catch (Exception $exception) {
            Log::error('Error: ' . $exception->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $exception->getMessage(),
                'data' => null,
            ];
        }
    }
}
