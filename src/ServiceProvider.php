<?php


namespace Plug2Team\ModelCached;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Plug2Team\ModelCached\Commands\FlushCommand;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
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
            FlushCommand::class
        ]);
    }
}
