<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
        Response::macro('success', function ($data = null, $code = 200, $msg = null, $meta = null) {
            return Response()->json([
                'code' => $code,
                'status' => true,
                'meta' => $meta,
                'data' => $data,
                'msg' => $msg
            ], $code)->header('Content-Type', 'application/json');
        });

        Response::macro('error', function ($code, $msg) {
            return Response()->json([
                'code' => $code,
                'status' => false,
                'data' => null,
                'msg' => $msg
            ], ($code > 99 && $code < 600) ? $code : 500)->header('Content-Type', 'application/json');
        });

        Schema::defaultStringLength(191);
    }
}
