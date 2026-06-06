<?php

namespace App\Providers;

use App\Services\Otp\ChannelOtpSender;
use App\Services\Otp\OtpSenderInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(OtpSenderInterface::class, function () {
            return new ChannelOtpSender();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
