<?php

namespace Mobieve\AuthClient\Middleware;

use Closure;
use \Auth;
use \User;
use \Team;
use \Exception;
use \JWTAuth;
use \Log;
use Mobieve\AuthClient\Exceptions\NotAnUserException;
use Mobieve\AuthClient\Exceptions\UserNotFoundException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class MobieveAuthMiddleware
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
            if (Auth::check()) { return $next($request); }

            JWTAuth::parseToken();

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
        } catch (NotAnUserException $e) {
            // Log::error($e);
            return response()->json([
                'message' => 'Unauthorized.',
                'reason' => 'You need to be an user to request.'
            ], 401);
        } catch (UserNotFoundException $e) {
            Log::error($e);
            return response()->json([
                'message' => 'Unauthorized.',
                'reason' => 'User not found.'
            ], 401);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => 'Unauthorized.'
            ], 401);
        }
    }
}