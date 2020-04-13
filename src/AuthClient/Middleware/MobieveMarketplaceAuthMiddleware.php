<?php

namespace Mobieve\AuthClient\Middleware;

use Closure;
use \Auth;
use \User;
use \Exception;
use \JWTAuth;
use \Log;
use Mobieve\AuthClient\Exceptions\NotAnUserException;
use Mobieve\AuthClient\Exceptions\UserNotFoundException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class MobieveMarketplaceAuthMiddleware
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

            $external_id = JWTAuth::getPayload()->get('user_id');
            $user = User::where('external_id', $external_id)->first();
            if (!$user) {
                throw new UserNotFoundException();
            }

            Auth::login($user);

            return $next($request);
        } catch (TokenExpiredException $e) {
            Log::error($e);
            return response()->json([
                'message' => 'Unauthorized.',
                'reason' => 'Token Expired.'
            ], 401);
        } catch (TokenInvalidException $e) {
            Log::error($e);
            return response()->json([
                'message' => 'Unauthorized.',
                'reason' => 'Token Invalid.'
            ], 401);
        } catch (NotAnUserException $e) {
            Log::error($e);
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

    public static function checkRequesterIdentity()
    {
        $isApi = JWTAuth::getPayload()->get('is_api_client');

        if ($isApi != null) {
            throw new NotAnUserException('is_api_client payload is present');
        }
    }
}