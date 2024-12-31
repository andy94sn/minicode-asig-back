<?php

namespace App\GraphQL\Mutations\Payment;

use App\Models\Order;
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
        'description' => 'Transaction Payment'
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
        try{
            $order = Order::where('token', trim($args['token']))->first();

            if(!$order){
                $message = $this->message($args['lang'], 'found');
                return new Error($message);
            }

            $params['amount'] = $order->price;
            $params['currency'] = 'MDL';
            $params['client'] = request()->ip();
            $params['id']     = $order->id;

            if (empty($params['amount']) || empty($params['currency']) || empty($params['client']) || empty($params['id'])) {
                $message = $this->message($args['lang'], 'invalid');
                return new Error($message);
            }

            $response = $this->payment->pay($params);

            if(!$response){
                $message = $this->message($args['lang'], 'transaction');
                return new Error($message);
            }

            return $response;
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Error('Something went wrong');
        }
    }

    private function message($language, string $error): string
    {
        $messages = [
            'ro' => [
                'invalid' => 'Date invalide',
                'error' => 'Ceva nu a mers bine',
                'param' => 'Parametri incorecți',
                'found' => 'Comanda nu există',
                'transaction' => 'Transacția a eșuat'
            ],
            'en' => [
                'invalid' => 'Invalid data',
                'error' => 'Something went wrong',
                'param' => 'Incorrect parameters',
                'found' => 'Order not exist',
                'transaction' => 'Transaction has been failed'
            ],
            'ru' => [
                'invalid' => 'Неверные данные',
                'error'   => 'Что-то пошло не так',
                'param' => 'Неверные параметры',
                'found'   => 'Comanda nu există',
                'transaction' => 'Транзакция не удалась'
            ]
        ];

        return $messages[$language][$error] ?? $messages['ro']['error'];;
    }
}
