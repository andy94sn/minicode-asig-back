<?php

namespace App\GraphQL\Mutations\Orders;

use App\Services\HelperService;
use App\Services\RcaApiService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use App\Models\Order;
use Exception;

class CreateOrderMutation extends Mutation
{
    protected RcaApiService $service;

    protected $attributes = [
        'name' => 'createOrder',
        'description' => 'Create Order',
        'model' => Order::class
    ];

    public function __construct(RcaApiService $service)
    {
        $this->service = $service;
    }

    public function type(): Type
    {
        return GraphQL::type('OrderResponse');
    }

    public function args(): array
    {
        return[
            'code' => [
                'name' => 'code',
                'type' => Type::nonNull(Type::string()),
                'description' => 'ID'
            ],
            'certificate' => [
                'name' => 'certificate',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Registration Number'
            ],
            'agreement' => [
                'name' => 'agreement',
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'Agreement'
            ],
            'start'  => [
                'name' => 'start',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Start Date'
            ],
            'possession'  => [
                'name' => 'possession',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Possession Right'
            ],
            'mode' => [
                'name' => 'mode',
                'type' => Type::string(),
                'description' => 'Mode'
            ],
            'zone' => [
                'name' => 'zone',
                'type' => Type::string(),
                'description' => 'Zone'
            ],
            'term' => [
                'name' => 'term',
                'type' => Type::string(),
                'description' => 'Term Insurance'
            ],
            'type' => [
                'name' => 'type',
                'type' => GraphQL::type('EnumInsurance'),
                'description' => 'Type',
            ],
            'phone' => [
                'phone' => 'phone',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Phone'
            ],
            'email' => [
                'email' => 'email',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Email'
            ],
            'person' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Person Type'
            ],
            'lang' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Language'
            ]
        ];
    }

    public function resolve($root, $args)
    {
        try {
            $params = [
                'email' => trim($args['email']),
                'phone' => trim($args['phone']),
                'code' => trim($args['code']),
                'certificate' => trim($args['certificate']),
                'type' => trim($args['type']),
                'info' => [
                    'zone' => $args['zone'] ?? null,
                    'term' => $args['term'] ?? null,
                    'mode' => $args['mode'] ?? null,
                    'validity' => $args['start'] ?? null,
                    'possession' => $args['possession'] ?? null,
                    'person_type' => $args['person'] ?? null,
                    'name' => $args['person'] ?? null
                ]
            ];

            $http_response_calculate = $this->service->calculate($args);

            if(isset($http_response_calculate['error'])){
                return new Error($http_response_calculate['error']);
            }

            $params['price'] = $http_response_calculate['primeSumMdl'];
            return Order::create($params);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return new Error(HelperService::message($args['lang'], 'error'));
        }
    }
}
