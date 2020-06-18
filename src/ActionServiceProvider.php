<?php

namespace Laraditz\Action;

use Illuminate\Support\ServiceProvider;

class ActionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/action.php' => $this->configPath('action.php'),
        ]);

        // register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                __NAMESPACE__ . '\Commands\ActionMakeCommand',
            ]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/action.php',
            'action'
        );
    }

    public function configPath($file)
    {
        if (function_exists('config_path')) {
            return config_path($file);
        } else {
            return base_path('config/' . $file);
        }
    }
}
