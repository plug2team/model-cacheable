<?php


namespace Plug2Team\ModelCacheable;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Plug2Team\ModelCacheable\Commands\FlushCommand;
use Plug2Team\ModelCacheable\Commands\ReIndexCommand;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/model_cached.php' => config_path('model_cached.php')
        ], 'config');
    }

    /**
     * @return void
     */
    public function register()
    {
        // register
        $this->app->singleton('cacheable', Strategy::class);

        $this->mergeConfigFrom(__DIR__.'/../config/model_cached.php', 'model_cached');

        // register commands
        $this->registerCommands();
    }

    /**
     * @return void
     */
    public function registerCommands() : void
    {
        $this->commands([
            FlushCommand::class,
            ReIndexCommand::class
        ]);
    }
}
