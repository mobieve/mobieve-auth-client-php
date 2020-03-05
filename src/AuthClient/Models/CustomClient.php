<?php

namespace Mobieve\AuthClient\Models;

use \GuzzleHttp\Client;
use \GuzzleHttp\RequestOptions;
use \GuzzleHttp\HandlerStack;
use \GuzzleHttp\Middleware;
use \GuzzleHttp\Exception\RequestException;
use \GuzzleHttp\Handler\CurlHandler;
use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\ResponseInterface;

class CustomClient {
  /**
   * This is a Singleton Custom Http Client.
   * 
   * It has an instance of GuzzleHttp\Client provided with Retry and Request Middleware.
   * 
   * The Request Middleware is responsible for put the cached token in the authorization header.
   * 
   * In case of Unauthorized response (401), the Retry Middleware will issue a new token to
   * OAuth Server and store it in cache.
   * 
   */

  private $client;

  public function __construct()
  {
    $stack = HandlerStack::create(new CurlHandler());

    $stack->push(Middleware::mapRequest($this->putAuthorizationHeader()));
    $stack->push(Middleware::retry($this->retryDecider(), $this->retryDelay()));

    $this->client = new Client(['handler' => $stack]);
  }

  private function retryDecider()
  {
    return function (
      $retries, 
      RequestInterface $request, 
      ResponseInterface $response = null, 
      RequestException $exception = null
    ) {
      if ($retries > 1) {
          return false;
      }

      if ($response && $response->getStatusCode() === 401) {
        // Refresh access token
        $this->refreshClientAccessToken();
        return true;
      }
      return false;
    };
  }

  private function retryDelay()
  {
    return function ($numberOfRetries) {
      return 500 * $numberOfRetries;
    };
  }

  private function putAuthorizationHeader()
  {
    return function (RequestInterface $request) {
      $token = $this->getClientAccessToken();
      $request = $request->withHeader('Authorization', 'Bearer ' . $token);
      return $request;
    };
  }

  public function get(string $url, array $params)
  {
    return $this->client->get($url, [
      RequestOptions::JSON => $params
    ]);
  }

  public function post(string $url, array $params)
  {
    return $this->client->post($url, [
      RequestOptions::JSON => $params
    ]);
  }

  public function put(string $url, array $params)
  {
    return $this->client->put($url, [
      RequestOptions::JSON => $params
    ]);
  }

  public function delete(string $url)
  {
    return $this->client->delete($url);
  }

  protected function getClientAccessToken()
  {
    $accessToken = \Cache::get('access_token');
    if (!isset($accessToken)) {
      $this->refreshClientAccessToken();
      $accessToken = \Cache::get('access_token');
    }
    return $accessToken;
  }

  private function refreshClientAccessToken()
  {
    $client = new Client();

    $response = $client->post(config('services.auth.url'), [
        RequestOptions::JSON => [
            'grant_type' => 'client_credentials',
            'client_id' => config('services.auth.client_id'),
            'client_secret' => config('services.auth.client_secret')
        ]
    ]);

    $json = json_decode((string) $response->getBody(), true);
    $accessToken = $json['access_token'];

    \Cache::put('access_token', $accessToken);
  }
}