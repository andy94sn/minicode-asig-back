<?php

namespace App\GraphQL\Mutations\Orders;

use App\Models\Order;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class DownloadFileMutation extends Mutation
{
    protected $attributes = [
        'name' => 'downloadFileMutation',
        'description' => 'Download File'
    ];

    public function type(): Type
    {
        return GraphQL::type('DownloadResponse');
    }

    public function args(): array
    {
        return [
            'token' => [
                'name' => 'token',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Token'
            ],
            'lang' => [
                'name' => 'lang',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Language'
            ]
        ];
    }

    /**
     * @throws Error
     */
    public function resolve($root, $args)
    {
        $lang  = $args['lang'];
        $token = $args['token'];

        try{
            $order = Order::find($token);

            if (!$order) {
                return new Error(HelperService::message($lang, 'found'));
            }

            $path = $order->link;

            if (!$path) {
                return new Error(HelperService::message($lang, 'found'));
            }

            $response = Http::get($path);

            if ($response->failed()) {
                return new Error(HelperService::message($lang, 'error'));
            }

            return [
                'fileContent' => base64_encode($response->body()),
                'fileName' => $order->policy . '.pdf',
                'mimeType' => $response->header('Content-Type')
            ];
        }catch(\Exception $exception){
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
