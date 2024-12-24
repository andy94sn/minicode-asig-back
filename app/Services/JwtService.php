<?php

namespace App\Services;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use Exception;
use Firebase\JWT\Key;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Log;

class JwtService
{

    public function generateTokens($admin): array
    {
        $now = time();
        $key = env('JWT_SECRET_KEY');
        $accessTokenTTL = env('JWT_TOKEN_EXPIRATION');
        $refreshTokenTTL = env('JWT_REFRESH_TOKEN_EXPIRATION');

        $accessTokenPayload = [
            'iss' => 'ozone_car',
            'sub' => $admin->id,
            'iat' => $now,
            'exp' => $now + $accessTokenTTL,
            'type' => 'access'
        ];

        $refreshTokenPayload = [
            'iss' => 'ozone_car',
            'sub' => $admin->id,
            'iat' => $now,
            'exp' => $now + $refreshTokenTTL,
            'type' => 'refresh'
        ];

        $accessToken = JWT::encode($accessTokenPayload, $key, 'HS256');
        $refreshToken = JWT::encode($refreshTokenPayload, $key, 'HS256');

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => $accessTokenTTL
        ];
    }

    /**
     * @throws Error
     */
    public function refreshToken($refreshToken, $admin): array
    {
        try {
            $key = env('JWT_SECRET_KEY');
            $decoded = JWT::decode($refreshToken, new Key($key, 'HS256'));

            if ($decoded->type !== 'refresh') {
                throw new Error('Invalid token');
            }

            if ($decoded->exp < time()) {
                throw new Error('Token has expired');
            }

            if ($decoded->sub !== $admin->id) {
                throw new \Exception('Invalid token');
            }


            return $this->generateTokens($admin);
        } catch (\Exception $exception) {
            throw new Error('Token is invalid or expired');
        }
    }


    /**
     * Decodifică un JWT și returnează payload-ul
     *
     * @param string $jwt
     * @return array|null
     */
    public function decodeJwt(string $jwt): ?array
    {
        $key = env('JWT_SECRET_KEY');

        try {
            $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
            return (array) $decoded;
        } catch (Exception) {
            return null;
        }
    }

    public function decodeJwtWithoutValidation(string $token): array
    {
        try {
            [$header, $payload, $signature] = explode('.', $token);
            $decodedPayload = json_decode(base64_decode($payload), true);

            if (!$decodedPayload) {
                throw new \Exception('Invalid token');
            }

            return $decodedPayload;
        } catch (\Exception $e) {
            Log::error('Failed to decode JWT without validation: ' . $e->getMessage());
            return [];
        }
    }

    public function isTokenExpired(string $token): bool
    {
        try {
            [$header, $payload, $signature] = explode('.', $token);
            $decodedPayload = json_decode(base64_decode($payload), true);

            if (isset($decodedPayload['exp'])) {
                $expirationTime = Carbon::createFromTimestamp($decodedPayload['exp']);
                return Carbon::now()->greaterThan($expirationTime);
            }

            throw new \Exception('Invalid token');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return true;
        }
    }

}
