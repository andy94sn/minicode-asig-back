<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Authorization
{
    protected array $allowed = [
        'https://ozoncar.md',
        'https://admin.ozoncar.md',
        'http://10.10.1.45:5000',
        'http://10.10.1.45:4000',
        'http://10.10.1.31:5173',
        'http://10.10.1.45:5173'
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
