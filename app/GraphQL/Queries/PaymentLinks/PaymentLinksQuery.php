<?php

namespace App\GraphQL\Queries\PaymentLinks;

use App\Models\Admin;
use App\Models\Order;
use App\Models\Translation;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class PaymentLinksQuery extends Query
{
    protected $attributes = [
        'name' => 'getPayments',
        'description' => 'Return Orders with Payment links',
        'model' => Order::class
    ];

    public function type(): Type
    {
        return GraphQL::type('OrderPagination');
    }

    public function args(): array
    {
        return [
            'phone' => [
                'type' => Type::string(),
                'description' => 'Search By Phone'
            ],
            'status' => [
                'type' => Type::string(),
                'description' => 'Search By Status'
            ],
            'email' => [
                'type' => Type::string(),
                'description' => 'Search By Email'
            ],
            'policy_number' => [
                'type' => Type::string(),
                'description' => 'Search By Policy Number'
            ],
            'type' => [
                'type' => Type::string(),
                'description' => 'Search By Type Orders'
            ],
            'perPage' => [
                'type' => Type::int(),
                'description' => 'Pagination'
            ],
            'page' => [
                'type' => Type::int(),
                'description' => 'Page'
            ],
            'orderBy' => [
                'type' => Type::string(),
                'description' => 'Order By'
            ],
            'sortBy' => [
                'type' => Type::string(),
                'description' => 'Sort By'
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
            $perPage = $args['perPage'] ?? 10;
            $page = $args['page'] ?? 1;


            if (!$auth) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-payments')) {
                return new Error(HelperService::message($lang, 'permission'));
            }

            $query = Order::query()->withPayments(); //Show created as payment links from agents
            
            if (!$auth->hasPermissionTo('manage-all-payments')) {
                $query->whereHas('paymentLink', function ($query) use ($auth) {
                    $query->where('admin_id', $auth->id);
                });
            }
            if (isset($args['phone'])) {
                $query->where('phone', 'like', '%' . $args['phone'] . '%');
            }

            if (isset($args['email'])) {
                $query->where('email', 'like', '%' . $args['email'] . '%');
            }

            if (isset($args['policy_number'])) {
                $query->where('code', 'like', '%' . $args['policy_number'] . '%');
            }

            if(isset($args['type'])){
                $type = HelperService::clean($args['type']);
                $query->where('type',  $type);
            }

            if(isset($args['status'])){
                $query->where('status',  $args['status']);
            }

            if(!empty($args['orderBy']) && !empty($args['sortBy'])){
                $query->orderBy($args['sortBy'], $args['orderBy']);
            }else{
                $query->orderBy('created_at', 'desc');
            }

            $orders = $query->paginate($perPage, ['*'], 'page', $page);

            $orders->getCollection()->transform(function ($order) {
                $transaction = DB::table('transactions')->where('order_id', $order->id)->first();
                $order->transaction = $transaction ? $transaction->pay_id : null;
                return $order;
            });

            return [
                'data' => $orders->items(),
                'meta' => [
                    'total' => $orders->total(),
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'from' => $orders->firstItem(),
                    'to' => $orders->lastItem()
                ]
            ];
        }catch(\Exception $exception){
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
