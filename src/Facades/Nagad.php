<?php

namespace Jeishanul\Nagad\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Nagad Facades
 * @author Jeishanul Haque Shishir <shishirjeishanul@gmail.com>
 * @version 1.0.0
 */


class Nagad extends Facade
{
  /**
   * Get the registered name of the component.
   *
   * @return string
   */
  protected static function getFacadeAccessor()
  {
    return 'nagad';
  }
}
