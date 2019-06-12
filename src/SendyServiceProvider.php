<?php

namespace Hocza\Sendy;

use Illuminate\Support\ServiceProvider;

/**
 * Class SendyServiceProvider
 *
 * @package Hocza\Sendy
 */
class SendyServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $app = $this->app;

        if (version_compare($app::VERSION, '5.0') < 0) {

            $this->package('hocza/sendy', 'sendy');

            // Register for exception handling
            $app->error(function (\Exception $exception) use ($app) {
                if ('Symfony\Component\Debug\Exception\FatalErrorException'
                    !== get_class($exception)
                ) {
                    $app['sendy']->notifyException($exception, null, "error");
                }
            });

            // Register for fatal error handling
            $app->fatal(function ($exception) use ($app) {
                $app['sendy']->notifyException($exception, null, "error");
            });
        } else {
          $this->publishes(array(
            __DIR__ . '/../config/sendy.php' => config_path('sendy.php')
          ));
        }

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Hocza\Sendy\Sendy', function ($app) {
            return new Sendy($app['config']['sendy']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['sendy'];
    }
}
