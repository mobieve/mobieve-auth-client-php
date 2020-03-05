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
        'JWTFactory' => Tymon\JWTAuth\Facades\JWTFactory::class,
        'JWTAuth' => Tymon\JWTAuth\Facades\JWTAuth::class,
        'MobieveClient' => Mobieve\AuthClient\Facades\CustomClient::class
    ],
  ```

  If user requests is needed you also need to include:

  ```php
    'aliases' => [
        ...
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'User' => App\User::class
    ],
  ```


  You also need to configure auth service info in config/services.php, like:

  ```php
    return [
      ...
      'auth' => [
          'client_id' => env('MOBIEVE_AUTH_CLIENT_ID'),
          'client_secret' => env('MOBIEVE_AUTH_CLIENT_SECRET')
      ]
    ];
  ```

  and include your personal MOBIEVE_AUTH_CLIENT_ID and MOBIEVE_AUTH_CLIENT_SECRET in your environment variables.
  
#### Usage

  ```php
    MobieveClient::get(string $url, array $params);
    MobieveClient::post(string $url, array $params);
    MobieveClient::put(string $url, array $params);
    MobieveClient::delete(string $url);
  ```
  
## Middleware

  Two different middleware classes are available.

  `MobieveClientAuthMiddleware` is used to ensure that the requester is a Client registered in Mobieve Auth server.

  `MobieveUserAuthMiddleware`, on the other hand, is used to ensure that the requester is an User registered in Mobieve Auth server and also will create an User copy in this server.

  To use Mobieve Middleware layer in order to check incoming requests authorization include following line in Http/Kernel.php:

  You need to include the middleware that you want to use in Http/Kernel.php file, as demonstrated below:

  ```php
    protected $routeMiddleware = [
      ...
      'mobieve.auth-client' => \Mobieve\AuthClient\Middleware\MovieveClientAuthMiddleware::class,
      'mobieve.auth-user' => \Mobieve\AuthClient\Middleware\MovieveClientAuthMiddleware::class
    ];
  ```

  and add `'mobieve.auth-client'` or `'mobieve.auth-user'` in all routes you need to protect, according to the desired behavior.