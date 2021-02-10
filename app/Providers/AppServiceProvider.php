<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        \Response::macro('attachment', function ($content) {

            $headers = [
                'Content-type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="download.pdf"',
            ];
        
            return \Response::make($content, 200, $headers);
        
        });
    }
}
