<?php

namespace Log\SDK;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class LogServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('log.client', function ($app) {
            $options = $app['config']->get('log');

            if (!isset($options['api_url'])) {
                throw new \InvalidArgumentException('Not found api_url config');
            }

            if (!isset($options['oauth']['url'])) {
                throw new \InvalidArgumentException('Not found oauth.url config');
            }

            if (!isset($options['oauth']['client_id'])) {
                throw new \InvalidArgumentException('Not found oauth.client_id config');
            }

            if (!isset($options['oauth']['client_secret'])) {
                throw new \InvalidArgumentException('Not found oauth.client_secret config');
            }

            return new LogClient($options['api_url']);
        });
    }

    public function boot()
    {
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$this->configPath() => config_path('log.php')], 'log');
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('log');
        }

        if ($this->app->runningInConsole()) {
            $this->registerMigrations();
        }
    }

    protected function configPath()
    {
        return __DIR__ . '/../config/log.php';
    }

    protected function registerMigrations()
    {
        return $this->loadMigrationsFrom(__DIR__.'/../migrations');
    }
}