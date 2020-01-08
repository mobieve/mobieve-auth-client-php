<?php

namespace Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use \Exception;
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

    public function handle($request, Closure $next)
    {

        try {
            \JWTAuth::parseToken();

            if (!$this->isApi()) {
                $user_id = $this->getUserId();
                $team_id = $this->getTeamId();

                // $request->input('user_id', $user_id);
                // $request->input('team_id', $team_id);
                $request->request->add([
                    'user_id' => $user_id,
                    'team_id' => $team_id
                ]);
            }
            return $next($request);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            \Log::error($e);
            return response()->json([
                'message' => 'Unauthorized.',
                'reason' => 'Token Expired.'
            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            \Log::error($e);
            return response()->json([
                'message' => 'Unauthorized.',
                'reason' => 'Token Invalid.'
            ], 401);
        } catch (Exception $e) {
            \Log::error($e);
            return response()->json([
                'message' => 'Unauthorized.'
            ], 401);
        }
    }

    /**
     * Check if current request is made by an application client or an api client
     * 
     * 
     */
    private function isApi()
    {
        $isApi = \JWTAuth::getPayload()->get('is_api_client');

        return $isApi || false;
    }

    /**
     * Get Mobieve User Id [or email?]
     * 
     */
    private function getUserId()
    {
        $user = \JWTAuth::getPayload()->get('user_id');

        if (!isset($user)) 
        {
            throw new \Tymon\JWTAuth\Exceptions\TokenInvalidException();
        }
        return $user;
    }

    private function getTeamId()
    {
        $team = \JWTAuth::getPayload()->get('team_id');

        if (!isset($team))
        {
            throw new \Tymon\JWTAuth\Exceptions\TokenInvalidException();
        }
        return $team;
    }

}
