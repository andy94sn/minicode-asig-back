<?php
namespace App\GraphQL\Mutations\Admins;

use App\Models\Admin;
use App\Services\HelperService;
use App\Services\JwtService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class LoginAdminMutation extends Mutation
{
    protected $attributes = [
        'name' => 'loginAdmin',
        'description' => 'Login Admin'
    ];

    protected JwtService $jwtService;

    public function type(): Type
    {
        return GraphQL::type('AdminResponse');
    }

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function args(): array
    {
        return [
            'email' => [
                'name' => 'email',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Email'
            ],
            'password' => [
                'name' => 'password',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Password'
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
            $email = HelperService::clean($args['email']);
            $admin = Admin::where('email', $email)->first();

            if (!$admin || !Hash::check($args['password'], $admin->password) || !$admin->status || !$admin->token) {
                return new Error(HelperService::message($lang, 'failed'));
            }

            $jwt = $this->jwtService->generateTokens($admin);
            $admin->role = $admin->roles->first()->permissions;

            return [
                'access_token' => $jwt['access_token'],
                'refresh_token' => $jwt['refresh_token'],
                'expires_at' => env('JWT_TOKEN_EXPIRATION'),
                'admin' => [
                    'token' => $admin->token,
                    'name' => $admin->name,
                    'email' => $admin->email,
                    'role' => $admin->roles->first(),
                    'permissions' => $admin->roles->first()->permissions
                ]
            ];
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
