<?php

namespace Mobieve\AuthClient\Middleware;

use Closure;
use \Exception;
use \JWTAuth;
use \Log;
use Mobieve\AuthClient\Exceptions\NotAClientException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class MobieveClientAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public static function handle($request, Closure $next)
    {
        try {
            JWTAuth::parseToken();

            self::checkRequesterIdentity();

            return $next($request);
        } catch (TokenExpiredException $e) {
            // Log::error($e);
            return response()->json([
                'message' => 'Unauthorized.',
                'reason' => 'Token Expired.'
            ], 401);
        } catch (TokenInvalidException $e) {
            // Log::error($e);
            return response()->json([
                'message' => 'Unauthorized.',
                'reason' => 'Token Invalid.'
            ], 401);
        } catch (NotAClientException $e) {
            // Log::error($e);
            return response()->json([
                'message' => 'Unauthorized.',
                'reason' => 'You need to be a client to request.'
            ], 401);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => 'Unauthorized.'
            ], 401);
        }
    }

    public static function checkRequesterIdentity()
    {
        $isApi = JWTAuth::getPayload()->get('is_api_client');

        if ($isApi == null) {
            throw new NotAClientException('Missing is_api_client payload');
        }
    }
}