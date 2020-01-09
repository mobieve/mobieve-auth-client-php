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
        'Client' => Mobieve\AuthClient\Facades\CustomClient::class

    ],
  ```
#### Usage

  ```php
    Client::get(string $url, array $params);
    Client::post(string $url, array $params);
    Client::put(string $url, array $params);
    Client::delete(string $url);
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