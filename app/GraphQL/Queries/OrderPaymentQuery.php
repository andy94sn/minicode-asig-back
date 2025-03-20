<?php

namespace App\GraphQL\Queries;

use App\Models\Order;
use App\Services\HelperService;
use Exception;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;

class OrderPaymentQuery extends Query
{
    protected $attributes = [
        'name'        => 'getOrderPayment',
        'description' => 'Return Order Payment',
        'model'       => Order::class
    ];

    public function type(): Type
    {
        return GraphQL::type('Order');
    }

    public function args(): array
    {
        return [
            'token' => [
                'name' => 'token',
                'type' => Type::nonNull(Type::string()),
                'description' => 'PayToken'
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
            $order = Order::where('token', $token)
            ->where('status', '<>', 'completed')
            ->whereHas('paymentLink', function ($query) {
                $query->where('status', true);
            })
            ->first();
            
            if(!$order){
                return new Error(HelperService::message($lang, 'found'));
            }
            // 

            return $order;
        }catch (Exception $exception){
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
