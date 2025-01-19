<?php

namespace App\GraphQL\Queries;

use App\Services\ModeService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Exception;

class ModesInsuranceQuery extends Mutation
{
    protected $attributes = [
        'name' => 'getModes',
        'description' => 'Return Modes Insurances'
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Mode'));
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
        try {
            $service = new ModeService($args['lang']);
            return $service->zones();
        } catch (Exception $exception) {
            Log::error('Error: ' . $exception->getMessage());
            return new Error('Error: ' . $exception->getMessage());
        }
    }
}
