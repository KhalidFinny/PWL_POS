<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class DebugJWTMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = JWTAuth::parseToken();
            Log::info('JWT Token: ' . $request->bearerToken());
            Log::info('Parsed Token Payload: ' . json_encode($token->getPayload()->toArray()));

            $sub = (int) $token->getPayload()->get('sub'); // Cast sub to integer
            Log::info('Casting sub to integer: ' . $sub);

            $user = \App\Models\UserModel::find($sub); // Manually retrieve the user
            if (!$user) {
                Log::error('User not found for sub: ' . $sub);
                throw new \Exception('User not found');
            }

            Log::info('Manually Retrieved User: ' . json_encode($user));

            // Use the authenticate method to set the user on the guard
            $userFromAuth = $token->authenticate();
            if ($userFromAuth === false) {
                Log::error('Authenticate failed for sub: ' . $sub);
                throw new \Exception('Authenticate failed');
            }

            Log::info('JWT Authenticated User: ' . json_encode($userFromAuth));

            auth('api')->setUser($userFromAuth);
            Log::info('User set on api guard: ' . json_encode(auth('api')->user()));

            $request->setUserResolver(function () use ($userFromAuth) {
                return $userFromAuth;
            });
            Log::info('User set on request: ' . json_encode($request->user()));
        } catch (\Exception $e) {
            Log::error('JWT Authentication Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
        return $next($request);
    }
}
