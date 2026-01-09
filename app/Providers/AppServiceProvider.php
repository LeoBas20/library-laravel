<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Redirect;
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
        /**
         * Fix for the "/home" refresh issue in role-based systems.
         * This ensures that the 'guest' middleware redirects users to the correct 
         * dashboard based on their role instead of a non-existent /home route.
         */
        Redirect::macro('intendedDashboard', function () {
            $user = Auth::user();
            if (!$user) {
                return redirect('/');
            }

            return strtolower($user->role) === 'admin' 
                ? redirect()->route('admin.dashboard') 
                : redirect()->route('student.dashboard');
        });
    }
}