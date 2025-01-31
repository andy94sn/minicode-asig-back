<?php

namespace App\GraphQL\Queries\Orders;

use App\Models\Admin;
use App\Models\Order;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;

class OrderQuery extends Query
{
    protected $attributes = [
        'name' => 'getOrder',
        'description' => 'View Order Details (Detalii despre comandă)',
        'model' => Order::class
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
                'description' => 'Token'
            ]
        ];
    }

    /**
     * @throws Error
     */
    public function resolve($root, $args)
    {
        $lang = $args['lang'] ?? 'ro';

        try{
            $auth = Admin::find(request()->auth['sub']);
            $token = HelperService::clean($args['token']);

            $order = Order::where('token', $token)->firstorFail();

            if (!$auth) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-orders')) {
                return new Error(HelperService::message($lang, 'permission'));
            }elseif(!$order) {
                return new Error(HelperService::message($lang, 'found').' - Order');
            }

            $order->refund = min($order->price, $order->refund);

            return $order;
        }catch(\Exception $exception){
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
