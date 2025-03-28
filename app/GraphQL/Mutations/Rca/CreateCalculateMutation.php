<?php

namespace App\GraphQL\Mutations\Rca;

use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Exception;
use GuzzleHttp\Client;
use App\Models\Admin;

class CreateCalculateMutation extends Mutation
{
    protected $attributes = [
        'name' => 'calculateMutation',
        'description' => 'CALCULATE RCA DATA'
    ];

    public function type(): Type
    {
        return GraphQL::type('Calculate');
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
            'code' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'IDNO/IDNP Person'
            ],
            'certificate' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Registration Number'
            ],
            'trailer_status' => [
                'type' => Type::boolean(),
                'description' => 'Trailer Status'
            ],
            'agreement' => [
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'Agreement confirmation'
            ],
            'zone' => [
                'type' => Type::string(),
                'description' => 'Zone (Type RCAE is required)'
            ],
            'term' => [
                'type' => Type::string(),
                'description' => 'Term Insurance (Type RCAE is required)'
            ],
            'lang' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Language'
            ],
            'type' => [
                'name' => 'type',
                'type' => new nonNull(GraphQL::type('EnumInsurance')),
                'description' => 'Type'
            ]
        ];
    }

    /**
     * @throws Error|GuzzleException
     */
    public function resolve($root, $args)
    {
        $lang = trim($args['lang']);
        $isTrailer = $args['trailer_status'] ?? false;

        try{

            $auth = null;
            if (!empty(request()?->auth['sub'])) {
                $auth = Admin::find(request()?->auth['sub']);
            }
  
            if ($auth && !$auth?->idno) {
                return new Error("Nu aveți setat IDNP!"); 
            }

            $args['agent_idnp'] = $auth?->idno ?? null;

            $response = $this->client->post('/api/calculate', [
                'json' => $args,
                'headers' => [
                    'Accept' => 'application/json'
                ],
                'timeout' => 30
            ]);

            $http_response = json_decode($response->getBody()->getContents(), true);

            if(isset($http_response['error'])){
                return new Error($http_response['error']);
            }

            if($isTrailer){
                $http_response['primeSumMdl'] = $this->roundUpToTwoDecimals($http_response['primeSumMdl']);
            }

            return $http_response;
        }catch (Exception $exception){
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }

    private function roundUpToTwoDecimals($number): float|int
    {
        $price = $number * 0.2;
        return ceil($price * 100) / 100;
    }
}
