<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Authorization
{
    protected array $allowed = [
        'https://primasig.md',
        'https://admin.primasig.md',
        'https://api.primasig.md',
        'http://127.0.0.1:8000'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('Origin') && !in_array($request->header('Origin'), $this->allowed)) {
            return response()->json([
                'message' => 'Authorization denied'
            ], 403);
        }
        return $next($request);
    }
}
