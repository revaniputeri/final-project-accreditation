<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Yajra\DataTables\Html\Builder;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

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
        Builder::useVite();

        // Set global Carbon timezone to application timezone
        Carbon::setLocale(config('app.locale'));
        date_default_timezone_set(config('app.timezone'));

        View::composer('*', function ($view) {
            /** @var UserModel|null $user */
            $user = Auth::user();
            $view->with('userRole', $user?->getRole());
            $view->with('userHasRole', fn($role) => $user ? $user->hasRole($role) : false);
        });
    }
}
