<?php

namespace App\GraphQL\Queries;

use App\Services\ZoneService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Exception;

class ZonesInsuranceQuery extends Mutation
{
    protected $attributes = [
        'name' => 'getZones',
        'description' => 'Return Zones GreenCard Insurances'
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Zone'));
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
            $service = new ZoneService($args['lang']);
            return $service->zones();
        } catch (Exception $exception) {
            Log::error('Error: ' . $exception->getMessage());
            return new Error('Error: ' . $exception->getMessage());
        }
    }
}
