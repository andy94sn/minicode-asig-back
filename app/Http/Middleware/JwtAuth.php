<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use App\Services\JwtService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\Key;

class JwtAuth
{
    protected JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $authHeader = $request->header('Authorization');
        if (!$authHeader) {
            return Response::json(['message' => 'Authorization denied'], 401);
        }

        $token = str_replace('Bearer ', '', $authHeader);

        try {
            $key = env('JWT_SECRET_KEY');
            $decoded = JWT::decode($token, new Key($key, 'HS256'));

            if ($decoded->iss !== 'motoasig' || $decoded->type !== 'access') {
                return Response::json(['message' => 'Invalid token'], 401);
            }

            $request->auth = (array) $decoded;
        } catch (ExpiredException $exception) {
            Log::error($exception->getMessage());
            return Response::json(['message' => 'Token has expired'], 401);
        } catch (BeforeValidException $exception) {
            Log::error($exception->getMessage());
            return Response::json(['message' => 'Token is not yet valid'], 401);
        } catch (SignatureInvalidException $exception) {
            Log::error($exception->getMessage());
            return Response::json(['message' => 'Invalid token signature'], 401);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return Response::json(['message' => 'Invalid token'], 401);
        }

        return $next($request);
    }
}
