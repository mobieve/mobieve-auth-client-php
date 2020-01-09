# mobieve-auth-client-php
Mobieve Auth Client PHP

This package provides Classes for Auth Clients made by Mobieve.

* Mobieve\AuthClient\Facades\CustomClient
* Mobieve\AuthClient\Middleware\MobieveAuthMiddleware
* Mobieve\AuthClient\Models\CustomClient
* Mobieve\AuthClient\Providers\CustomClientServiceProvider

## Mobieve Custom HTTP Client

#### Configuration

  In config/app.php include:

  ```php
    'providers' => [
        ...
        Mobieve\AuthClient\Providers\CustomClientServiceProvider::class

    ],
  ```

  and:

  ```php
    'aliases' => [
        ...
        'MobieveClient' => Mobieve\AuthClient\Facades\CustomClient::class

    ],
  ```


  You also need to configure auth service info in config/services.php, like:

  ```php
    return [
      ...
      'auth' => [
          'url' => env('MOBIEVE_AUTH_URL'),
          'client_id' => env('MOBIEVE_AUTH_CLIENT_ID'),
          'client_secret' => env('MOBIEVE_AUTH_CLIENT_SECRET')
      ]
    ];
  ```

  and include MOBIEVE_AUTH_URL and your personal MOBIEVE_AUTH_CLIENT_ID and MOBIEVE_AUTH_CLIENT_SECRET in your environment variables.
  
#### Usage

  ```php
    MobieveClient::get(string $url, array $params);
    MobieveClient::post(string $url, array $params);
    MobieveClient::put(string $url, array $params);
    MobieveClient::delete(string $url);
  ```
  
## Middleware

  To use Mobieve Middleware layer in order to check incoming requests authorization include following line in Http/Kernel.php:

  ```php
    protected $routeMiddleware = [
      ...
      'mobieve.auth' => \Mobieve\AuthClient\Middleware\MovieveAuthMiddleware::class
    ];
  ```

  and add `'mobieve.auth'` in all routes you need to protect.