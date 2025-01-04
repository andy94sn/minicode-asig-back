<?php

namespace App\GraphQL\Mutations\Orders;

use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use App\Models\Order;
use Exception;

class CreateOrderMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createOrder',
        'description' => 'Create Order',
        'model' => Order::class
    ];

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

            $sessionPrice = session('Price');
            $sessionData = ['Price' => $sessionPrice];
            $signature = request()->cookie('session_signature');

            if (!$signature || !$this->isValidSignature($signature, $sessionData)) {
                return new Error(HelperService::message($args['lang'], 'error'));
            }

            if (!$sessionPrice) {
                return new Error(HelperService::message($args['lang'], 'error'));
            }

            $params['price'] = $sessionPrice;
            return Order::create($params);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return new Error(HelperService::message($args['lang'], 'error'));
        }
    }

    private function isValidSignature($signature, $sessionData)
    {
        $secretKey = env('HASH_SECRET_KEY');
        $calculatedSignature = hash_hmac('sha256', json_encode($sessionData), $secretKey);
        return $signature === $calculatedSignature;
    }
}
