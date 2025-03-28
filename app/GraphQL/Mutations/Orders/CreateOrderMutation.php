<?php

namespace App\GraphQL\Mutations\Orders;

use App\Models\Admin;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use GuzzleHttp\Client;
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
    private Client $client;

    public function type(): Type
    {
        return GraphQL::type('OrderResponse');
    }

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => env('API_RCA_EXTERNAL'),
            'timeout' => 10,
        ]);
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
            'trailer_id' => [
                'type' => Type::string(),
                'description' => 'Trailer ID'
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
            'name' => [
                'type' => Type::string(),
                'description' => 'Person Name'
            ],
            'lang' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Language'
            ],
            'vehicle_data' => [
                'type' => Type::string(),
                'description' => 'Vehicle data'
            ],
            'vehicle_insured' => [
                'type' => Type::string(),
                'description' => 'Vehicle insured'
            ],
            'vehicle_owner' => [
                'type' => Type::string(),
                'description' => 'Vehicle owner'
            ],
        ];
    }

    public function resolve($root, $args)
    {

        try {
            $auth = Admin::find(request()?->auth['sub']);

            if ($auth && !$auth?->idno) {
                return new Error("Nu aveți setat IDNP!"); 
            }

            $isTrailer = (bool)$args['trailer_id'];

            $params = [
                'email' => trim($args['email']),
                'phone' => trim($args['phone'] ?? ''),
                'code' => trim($args['code']),
                'trailer_id' => trim($args['trailer_id'] ?? ''),
                'certificate' => trim($args['certificate']),
                'type' => trim($args['type' ?? '']),
                'zone' => $args['zone'] ?? null,
                'term' => $args['term'] ?? null,
                'mode' => $args['mode'] ?? null,
                'validity' => $args['start'] ?? null,
                'possession' => $args['possession'] ?? null,
                'person_type' => $args['person'] ?? null,
                'name' => $args['name'] ?? null,
                'lang' => $args['lang'],
                'agent_idnp' => $auth?->idno ?? null
            ];

            $response = $this->client->post('api/calculate', [
                'json' => $params,
                'headers' => [
                    'Accept' => 'application/json'
                ],
            ]);

            $http_response = json_decode($response->getBody()->getContents(), true);

            if(isset($http_response['error'])){
                return new Error($http_response['error']);
            }

            $data = [
                'email' => trim($args['email']),
                'phone' => trim($args['phone'] ?? ''),
                'code' => trim($args['code']),
                'certificate' => trim($args['certificate']),
                'type' => trim($args['type'] ?? ''),
                'info' => [
                    'zone' => $args['zone'] ?? null,
                    'term' => $args['term'] ?? null,
                    'mode' => $args['mode'] ?? null,
                    'validity' => $args['start'] ?? null,
                    'possession' => $args['possession'] ?? null,
                    'name' => $args['person'] ?? null,
                    'trailer_id' => trim($args['trailer_id'] ?? '')
                ],
                'lang' => $args['lang']
            ];


            if($http_response){
                $data['info']['person_type'] = isset($http_response['firstName']) ? 1 : 2;

                if($isTrailer){
                    $data['price'] = $this->roundUpToTwoDecimals($http_response['primeSumMdl']);
                }else{
                    $data['price'] = $http_response['primeSumMdl'];
                }
            }

            $order = Order::create($data);

            try {

                if ($order->paymentLink) {
                    throw new \Exception('Link de plată deja a fost creat');
                }
    
                #Generate payment link
                $order->refresh()->paymentLink()->create([
                    'admin_id'          => $auth->id,
                    'trailer_id'        => trim($args['trailer_id']),
                    'vehicle_data'      => $args['vehicle_data'],
                    'vehicle_insured'   => $args['vehicle_insured'],
                    'vehicle_owner'     => $args['vehicle_owner'],
                    'name'              => trim($args['name']),
                ]);
            } catch (\Exception $e) {

            }
        
            
            return $order;
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return new Error(HelperService::message($args['lang'], 'error'));
        }
    }

    private function roundUpToTwoDecimals($number): float|int
    {
        $price = $number * 0.2;
        return ceil($price * 100) / 100;
    }
}
