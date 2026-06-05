<?php

namespace App\Providers;

use App\Services\Otp\LogOtpSender;
use App\Services\Otp\OtpSenderInterface;
use App\Services\Otp\SmsOtpSender;
use App\Services\Otp\WhatsAppOtpSender;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(OtpSenderInterface::class, function () {
            return match (config('polla.otp_provider')) {
                'sms' => new SmsOtpSender(),
                'whatsapp' => new WhatsAppOtpSender(),
                default => new LogOtpSender(),
            };
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
