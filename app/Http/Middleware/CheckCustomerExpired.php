<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class CheckCustomerExpired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        try {
            $token = JWTAuth::getToken(); // reads from Authorization header automatically
            if (!$token) {
                Log::debug('CheckCustomerExpired: no token on request');
                return $next($request); // public route or not sending a token
            }

            $payload = JWTAuth::setToken($token)->getPayload();
            $userId  = $payload->get('sub'); // default claim is the user's id

            Log::debug('CheckCustomerExpired: token decoded', ['userId' => $userId]);

            if ($userId && Cache::has("expired_customer_{$userId}")) {
                Log::notice('CheckCustomerExpired: blocked expired customer', ['userId' => $userId]);

                return response()->json([
                    'status'  => 'error',
                    'message' => 'Unauthenticated'
                ], 401);
            }
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            Log::warning('CheckCustomerExpired: invalid/expired token', ['error' => $e->getMessage()]);

            return response()->json([
                'status'  => 'error',
                'message' => app()->getLocale() == 'en'
                    ? 'Invalid or expired token'
                    : 'التوكن غير صالح أو منتهي الصلاحية',
                'data'    => [],
            ], 401);
        } catch (\Exception $e) {
            Log::error('CheckCustomerExpired: unexpected error', ['error' => $e->getMessage()]);
            // fail-closed is safer; but continue to next if you prefer
            return response()->json([
                'status'  => 'error',
                'message' => app()->getLocale() == 'en'
                    ? 'Authentication error'
                    : 'خطأ في المصادقة',
                'data'    => [],
            ], 401);
        }

        return $next($request);
    }
}
