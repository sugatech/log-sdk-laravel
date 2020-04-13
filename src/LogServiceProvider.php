<?php

namespace Log\SDK;

use Illuminate\Support\ServiceProvider;

class LogServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('log.client', function ($app) {
            $options = $app['config']->get('log');

            if (!isset($options['api_url'])) {
                throw new \InvalidArgumentException('Not found api_urL config');
            }

            if (!isset($options['access_token'])) {
                throw new \InvalidArgumentException('Not found access_token config');
            }

            return new LogClient($options['api_url'], $options['access_token']);
        });
    }

    public function boot()
    {
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$this->configPath() => config_path('log.php')], 'log');
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('log');
        }
    }

    protected function configPath()
    {
        return __DIR__ . '/../config/log.php';
    }
}