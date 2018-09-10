<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Laravel\Horizon\Horizon;

class AppServiceProvider extends ServiceProvider {
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        Schema::defaultStringLength(191);

        //Authenticate condition to show the horizon page
        Horizon::auth(function ($request) {
            if (session('horizon_admin'))
                return true;
            else
                return false;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        /**
         * Get common request variables from zendesk
         */


    }
}
