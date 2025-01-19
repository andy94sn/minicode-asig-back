<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class PaymentService
{

    protected Client $client;
    protected string $baseURL;
    protected string $version;

    public function __construct()
    {
        $this->baseURL = rtrim(env('MAIB_API_URL'), '/');
        $this->version = env('MAIB_API_VERSION');
        $this->client = new Client(['base_uri' => $this->baseURL]);
    }

    /**
     * Token Payment
     */
    private function token(): ?string
    {
        try {
            $endpoint = "/{$this->version}/generate-token";
            $payload = [
                'projectId' => env('MAIB_PROJECT_ID'),
                'projectSecret' => env('MAIB_PROJECT_SECRET'),
            ];
            $response = $this->client->post($endpoint, [
                'json' => $payload,
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if (isset($data['ok']) && $data['ok'] && isset($data['result']['accessToken'])){
                return (string)$data['result']['accessToken'];
            } else {
                return null;
            }
        } catch (Exception $e) {
            Log::error('Token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Transaction Payment
     */
    public function pay(array $args): ?array
    {
        try{
            $endpoint = "/{$this->version}/pay";
            $id = $args['id'];
            $token = $this->token();
            $payload = [
                'amount'   => $args['amount'],
                'currency' => $args['currency'],
                'description' => env('APP_NAME'),
                'clientIp' => $args['client'],
                'callbackUrl' => env('MAIB_CALLBACK_URL'),
                'okUrl'       => env('MAIB_SUCCESS_URL'),
                'failUrl'     => env('MAIB_FAILED_URL')
            ];

            if($token) {
                $response = $this->client->post($endpoint, [
                    'json' => $payload,
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json'
                    ]
                ]);

                $data = json_decode($response->getBody(), true);

                if (isset($data['ok']) && $data['ok'] && isset($data['result']['payId'])) {
                    DB::table('transactions')->insert([
                        'pay_id' => $data['result']['payId'],
                        'order_id' => $id
                    ]);

                    return [
                        'status' => $data['ok'],
                        'result' => [
                            'transaction' => (string)$data['result']['payUrl']
                        ]
                    ];
                } else {
                    return null;
                }
            }else{
                return null;
            }

        }catch (Exception $exception) {
            Log::error('Transaction: ' . $exception->getMessage());
            return null;
        }
    }


    public function refund(array $args): ?array
    {
        try {
            if (!isset($args['id']) || !isset($args['amount'])) {
                return ['status' => false, 'message' => 'Invalid input data'];
            }

            $endpoint = "/{$this->version}/refund";
            $id = $args['id'];
            $token = $this->token();

            $payload = [
                'refundAmount' => $args['amount'],
                'payId' => $id,
            ];

            if ($token) {
                $response = $this->client->post($endpoint, [
                    'json' => $payload,
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ]
                ]);

                $data = json_decode($response->getBody(), true);

                if (isset($data['ok']) && $data['ok'] && isset($data['result']['payId'])) {
                    DB::table('transactions')->where([
                        'pay_id' => $data['result']['payId'],
                    ])->delete();

                    return [
                        'status' => $data['ok']
                    ];
                } else {
                    return [
                        'status' => false,
                    ];
                }
            } else {
                return [
                    'status' => false
                ];
            }
        } catch (Exception $exception) {
            Log::error('Transaction: ' . $exception->getMessage());
            return [
                'status' => false
            ];
        }
    }

}
