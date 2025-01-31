<?php

namespace App\GraphQL\Mutations\Payment;

use App\Models\Order;
use App\Services\HelperService;
use App\Services\PaymentService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Exception;

class CreateTransactionMutation extends Mutation
{
    protected PaymentService $payment;
    protected $attributes = [
        'name' => 'transactionMutation',
        'description' => 'Create Transaction Payment MAIB'
    ];

    public function __construct(PaymentService $payment)
    {
        $this->payment = $payment;
    }

    public function type(): Type
    {
        return GraphQL::type('Transaction');
    }

    public function args(): array
    {
        return [
            'lang' => [
                'name' => 'lang',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Language'
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
    public function resolve($root, $args): array|Error
    {
        $lang = $args['lang'];

        try{
            $order = Order::where('token', trim($args['token']))->first();

            if(!$order){
                return new Error(HelperService::message($lang, 'found'));
            }

            $params['amount'] = $order->price;
            $params['currency'] = 'MDL';
            $params['client'] = request()->ip();
            $params['id']     = $order->id;

            if (empty($params['amount']) || empty($params['client']) || empty($params['id'])) {
                return new Error(HelperService::message($lang, 'invalid'));
            }

            $response = $this->payment->pay($params);

            if(!$response){
                return new Error(HelperService::message($lang, 'error'));
            }

            return $response;
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
