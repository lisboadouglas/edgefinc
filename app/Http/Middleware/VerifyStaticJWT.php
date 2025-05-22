<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyStaticJWT
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expectedToken = config('services.static_jwt.key');
        if($request->bearerToken() !== $expectedToken) {
            return response()->json([
                'status' => 'false',
                'code' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }
        return $next($request);
    }
}
