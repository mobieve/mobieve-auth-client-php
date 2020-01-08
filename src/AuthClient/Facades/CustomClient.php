<?php

namespace Mobieve\AuthClient\Facades;

use Illuminate\Support\Facades\Facade;

class CustomClient extends Facade
{
  protected static function getFacadeAccessor()
  {
    return 'customclient';
  }
}