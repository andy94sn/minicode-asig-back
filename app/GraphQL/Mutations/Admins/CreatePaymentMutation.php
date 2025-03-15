<?php

namespace App\GraphQL\Mutations\Admins;

use App\Models\Admin;
use App\Models\Order;
use App\Models\Role;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreatePaymentMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createPayment',
        'description' => 'create order payment'
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

            $order = Order::where('token', $token)->first();

            if(!$order){
                return new Error(HelperService::message($lang, 'found'));
            }elseif(!$auth){
                return new Error(HelperService::message($lang, 'denied'));
            }
            // elseif(!$auth->hasPermissionTo('manage-payments')){
            //     return new Error(HelperService::message($lang, 'permission'));
            // }

            if ($order->paymentLink) {
                throw new \Exception('Link de plată deja a fost creat');
            }

            #Generate payment link
            $order->paymentLink()->create([
                'admin_id'  => $auth->id
            ]);
            return $order;
            
            // return env('APP_FRONT_URL','http://motoasig.md') .  '/' . $token;
        }catch(\Exception $exception){
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
