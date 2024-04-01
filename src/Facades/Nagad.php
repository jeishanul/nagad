<?php

namespace Jeishanul\Nagad\Facades;

/**
 * Nagad Facades
 * @author Jeishanul Haque Shishir <shishirjeishanul@gmail.com>
 * @version 1.0.0
 */

use Illuminate\Support\Facades\Facade;

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
