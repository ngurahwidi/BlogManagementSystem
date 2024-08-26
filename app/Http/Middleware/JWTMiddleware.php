<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JWTMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $error) {
            if ($error instanceof TokenInvalidException){
                return response()->json([
                    'status'=> 'Error',
                    'code'=> 401,
                    'message' => 'Token Invalid',
                    'data' => []
                ], 401);
            }
            else if ($error instanceof TokenExpiredException){
                return response()->json([
                    'status'=> 'Error',
                    'code'=> 401,
                    'message' => 'Token Expired',
                    'data' => []
                ], 401);
            }
            else{
                return response()->json([
                    'status'=> 'Error',
                    'code'=> 401,
                    'message' => 'Authorization Token not found',
                    'data' => []
                ], 401);
            }
        }

        return $next($request);
    }
}
