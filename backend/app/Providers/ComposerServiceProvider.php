<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Http\Composers\GlobalComposer;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
        /*
         * Global
         */
        View::composer(
        // This class binds the $logged_in_user variable to every view
            '*',
            GlobalComposer::class
        );
    }
}
