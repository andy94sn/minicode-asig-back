<?php

namespace App\GraphQL\Mutations\Rca;

use App\Mail\OrderMail;
use App\Models\Order;
use App\Models\Setting;
use App\Services\HelperService;
use Exception;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateDocumentMutation extends Mutation
{

    protected $attributes = [
        'name' => 'documentMutation',
        'description' => 'Create Document Rca Data'
    ];
    private Client $client;

    public function type(): Type
    {
        return GraphQL::type('Document');
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
        return [
            'token' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Token Order'
            ],
            'lang' => [
                'name' => 'lang',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Language'
            ]
        ];
    }


    public function resolve($root, $args)
    {
        $lang = trim($args['lang']);
        $token = HelperService::clean($args['token']);

        try{
            $order = Order::where('token', $token)->first();

            if(!$order){
                return new Error(HelperService::message($lang, 'found'));
            }

            $params = $order->toArray();
            $params['lang'] = $lang;
            $params['agent_idnp'] = $order?->paymentLink?->agent?->idno ?? null;

            $response = $this->client->post('/api/save', [
                'json' => $params,
                'headers' => [
                    'Accept' => 'application/json'
                ],
                'timeout' => 30
            ]);

            $http_response = json_decode($response->getBody()->getContents(), true);
            
            if(isset($http_response['error'])){
                return new Error(HelperService::message($lang, 'invalid'));
            }

            if($http_response){
               
                $data = [];
                
                if(!isset($order->name)){
                    $data['name'] = $http_response['name'] ?? '';
                }

                $data['contract_number'] = $http_response['contract_number'];

                foreach($http_response['documents'] as $key => $document){
                    $data[$key] = $document;
                    if($key == 'policy'){
                        $data['link'] = env('RCA_APP_URL').'/storage/documents/'.$document;
                    }
                    if($key == 'demand'){
                        $data['demand_link'] = env('RCA_APP_URL').'/storage/documents/'.$document;
                    }
                }

                $isUpdated = $order->update($data);

                if($isUpdated){
                    $this->sendEmail($order, $lang, 'client');
                    $this->sendEmail($order, $lang, 'admin');
                }
            }

            return $http_response;
        }catch (Exception $exception) {
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }

    private function sendEmail($order, $lang, $to): void
    {
        #Admin emails
        $emails = explode(',', env('RCA_ADMIN_EMAILS'));

        $files = array();
        $type  = $order->type;
        $files[] =  env('RCA_APP_URL').'/storage/documents/' . $order->policy;
        $files[] =  env('RCA_APP_URL').'/storage/documents/'. $order->contract;

        if($to === 'client'){
            Mail::to($order->email)->send(new OrderMail($files, $type, $lang));
        }

        if($to === 'admin'){
            $files[] = env('RCA_APP_URL').'/storage/documents/'.$order->demand;
            if(count($emails) > 0){
                foreach($emails as $email){
                    Mail::to($email)->send(new OrderMail($files, $type, $lang));
                }
            }
        }
    }
}
