<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure our 'role' alias is registered on the router.
        // This makes the alias available even if some route/middleware cache or
        // other loading order issue prevented it from being registered elsewhere.
        try {
            $router = $this->app->make(\Illuminate\Routing\Router::class);
            $router->aliasMiddleware('role', \App\Http\Middleware\EnsureRole::class);
        } catch (\Throwable $e) {
            // swallow — we don't want boot to break the app if something odd happens.
            // The real error (if any) will appear in logs and we can debug further.
            \Log::warning('Could not register role middleware alias: '.$e->getMessage());
        }
    }
}
