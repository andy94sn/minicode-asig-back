<?php

namespace App\GraphQL\Queries;

use App\Services\PersonsService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Exception;

class PersonsQuery extends Mutation
{
    protected $attributes = [
        'name' => 'getPersons',
        'description' => 'Return Person Types'
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Person'));
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
            $service = new PersonsService($args['lang']);
            return $service->types();
        } catch (Exception $exception) {
            Log::error('Error: ' . $exception->getMessage());
            return new Error('Error: ' . $exception->getMessage());
        }
    }
}
