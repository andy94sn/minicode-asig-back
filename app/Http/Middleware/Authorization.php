<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Authorization
{

    protected array $allowed = [
        '10.10.1.45',
        '10.10.1.25',
        '10.10.1.31',
        'ozoncar.md'
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

        if (env('APP_ENV') === 'production' && !in_array($request->getHost(), $this->allowed)) {
            return response()->json(['message' => 'Authorization denied.'], 403);
        }elseif(!in_array($request->ip(), $this->allowed)) {
            return response()->json(['message' => 'Authorization denied.'], 403);
        }

        return $next($request);
    }
}
