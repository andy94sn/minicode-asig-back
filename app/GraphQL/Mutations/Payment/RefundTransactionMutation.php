<?php

namespace App\GraphQL\Mutations\Payment;

use App\Models\Admin;
use App\Models\Order;
use App\Services\HelperService;
use App\Services\PaymentService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Exception;

class RefundTransactionMutation extends Mutation
{
    protected PaymentService $payment;
    protected $attributes = [
        'name' => 'refundMutation',
        'description' => 'Transaction Refund Payment'
    ];

    public function __construct(PaymentService $payment)
    {
        $this->payment = $payment;
    }

    public function type(): Type
    {
        return GraphQL::type('Boolean');
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Pay ID'
            ],
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
            $order = Order::where('token', trim($args['token']))->first();

            if(!$auth) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$order){
                return new Error(HelperService::message($lang, 'found'));
            }elseif(!$auth->hasPermissionTo('manage-refund')) {
                return new Error(HelperService::message($lang, 'permission'));
            }

            if($order->status === 'completed'){
                $params['amount'] = $order->price;
                $params['id']     = $args['id'];

                if (empty($params['amount']) || empty($params['id'])) {
                    $message = $this->message($lang, 'invalid');
                    return new Error($message);
                }

                $response = $this->payment->refund($params);

                if(!$response){
                    return new Error(HelperService::message($lang, 'refund'));
                }

                if ($response['status'] === true) {
                    $order->status = 'cancel';
                    $order->refund = $order->price;
                    $order->save();

                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
