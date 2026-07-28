<?php

namespace Dgiftedx\VisitorLogger;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use Dgiftedx\VisitorLogger\Middleware\LogVisitorData;
use Dgiftedx\VisitorLogger\VisitorLoggerManager;

class VisitorLoggerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/visitor-logger.php', 'visitor-logger');

        $this->app->singleton('visitor-logger', function () {
            return new VisitorLoggerManager();
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/visitor-logger.php' => config_path('visitor-logger.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/../resources/js/visitor-logger.js' => public_path('vendor/visitor-logger.js'),
        ], 'assets');

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        $this->app->make(Kernel::class)->pushMiddleware(LogVisitorData::class);
    }
}
