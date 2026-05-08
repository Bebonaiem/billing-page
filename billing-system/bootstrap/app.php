<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\EnsureTempDir::class,
        ]);
        
        // Register middleware aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'client' => \App\Http\Middleware\ClientMiddleware::class,
        ]);
    })
    ->withSchedule(function ($schedule) {
        // Daily at midnight - generate recurring invoices
        $schedule->command('billing:generate-invoices')->daily();
        
        // Daily at 1 AM - add late fees
        $schedule->command('billing:add-late-fees')->dailyAt('01:00');
        
        // Daily at 2 AM - auto-suspend overdue services
        $schedule->command('billing:auto-suspend')->dailyAt('02:00');
        
        // Daily at 3 AM - process scheduled cancellations
        $schedule->command('billing:process-cancellations')->dailyAt('03:00');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
