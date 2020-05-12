<?php

namespace Mobieve\AuthClient\Middleware;

use Closure;
use \Auth;
use \User;
use \Exception;
use \JWTAuth;
use \Log;
use Mobieve\AuthClient\Exceptions\NotAnUserException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class MobieveUserAuthMiddleware
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
            self::checkRequesterIdentity();

            $auth_id = JWTAuth::getPayload()->get('user_id');
            $user = User::where('auth_id', $auth_id)->first();
            if (!$user) {
                $name = JWTAuth::getPayload()->get('user_name');
                $email = JWTAuth::getPayload()->get('user_email');
                $user = User::create([
                    'auth_id' => $auth_id,
                    'name' => $name,
                    'email' => $email,
                    'password' => 'null'
                ]);
                $user->assign("member");
            }

            Auth::login($user);

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

        if ($isApi != null) {
            throw new NotAnUserException('is_api_client payload is present');
        }
    }
}