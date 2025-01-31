<?php

namespace App\GraphQL\Queries;

use App\Services\HelperService;
use App\Services\ModeService;
use App\Services\PossessionService;
use App\Services\ZoneService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Exception;

class PossessionsInsuranceQuery extends Mutation
{
    protected $attributes = [
        'name' => 'getPossessions',
        'description' => 'Return Possessions Rights Insurances (Dreptul de posesiune a autovehiculului)'
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Possession'));
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
            $service = new PossessionService($args['lang']);
            return $service->possessions();
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
