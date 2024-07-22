<?php

namespace App\Providers;

use App\Actions\SendOrderCreatedNotification;
use App\Actions\SubmitOrder;
use App\Events\CreateOrder;
use Illuminate\Support\Facades\Event;
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
        Event::listen(CreateOrder::class, SubmitOrder::class);
    }
}
