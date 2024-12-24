<?php

namespace App\GraphQL\Mutations\Admins;

use App\Models\Admin;
use App\Services\HelperService;
use App\Services\JwtService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class RefreshTokenMutation extends Mutation
{
    protected $attributes = [
        'name' => 'refreshToken',
        'description' => 'Refresh Token'
    ];

    protected JwtService $jwtService;

    public function type(): Type
    {
        return GraphQL::type('AdminResponse');
    }

    public function args(): array
    {
        return [
            'refresh_token' => [
                'name' => 'refresh_token',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Refresh Token Valid'
            ],
            'access_token' => [
                'name' => 'access_token',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Access Token Invalid'
            ]
        ];
    }

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * @throws Error
     */
    public function resolve($root, $args)
    {
        $lang = $args['lang'] ?? 'ro';

        try{
            $refreshToken = HelperService::clean($args['refresh_token']);
            $accessToken = HelperService::clean($args['access_token']);

            $token = $this->jwtService->decodeJwtWithoutValidation($accessToken);
            $admin = Admin::find($token['sub']);

            if(!$admin){
                return new Error(HelperService::message($lang, 'denied'));
            }

            $tokens = $this->jwtService->refreshToken($refreshToken, $admin);

            return [
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'expires_at' => env('JWT_TOKEN_EXPIRATION'),
                'admin' => $admin
            ];
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
