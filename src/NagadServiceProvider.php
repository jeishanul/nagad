<?php

namespace Jeishanul\Nagad;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
/**
 * Service Provider class
 * @author Jeishanul Haque Shishir <shishirjeishanul@gmail.com>
 */

class NagadServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap any application services.
   *
   * @return void
   */
   public function boot()
   {
       $this->publishes([
        __DIR__ . '/../config/nagad.php' => config_path('nagad.php'),
      ], 'config');

       AliasLoader::getInstance()->alias('NagadPayment', 'Jeishanul\Nagad\Facades\Nagad');
   }

  /**
   * Register any application services.
   *
   * @return void
   */
   public function register()
   {
       $this->app->bind('nagad', function () {
           return new Nagad;
       });
   }
}
