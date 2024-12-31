<?php

namespace App\GraphQL\Queries;

use App\Models\Order;
use App\Services\HelperService;
use Exception;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Query;

class OrderStatusQuery extends Query
{
    protected $attributes = [
        'name'        => 'statusOrder',
        'description' => 'Return Order Status',
        'model'       => Order::class
    ];

    public function type(): Type
    {
        return Type::boolean();
    }

    public function args(): array
    {
        return [
            'token' => [
                'name' => 'token',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Token'
            ],
            'lang' => [
                'name' => 'lang',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Language'
            ]
        ];
    }

    /**
     * @throws Error
     */
    public function resolve($root, array $args)
    {
        $lang = trim($args['lang']);

        try {
            $token = HelperService::clean($args['token']);
            $order = Order::where('token', $token)->first();

            if(!$order){
                return new Error(HelperService::message($lang, 'found'));
            }

            if($order->status === 'completed'){
                return true;
            }

            return false;
        }catch (Exception $exception){
            Log::error('Error: ' . $exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
