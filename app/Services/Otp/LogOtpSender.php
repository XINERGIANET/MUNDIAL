<?php

namespace App\Services\Otp;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class LogOtpSender implements OtpSenderInterface
{
    public function send(User $user, string $plainCode, string $channel): array
    {
        Log::info('OTP verification code generated', [
            'user_id' => $user->id,
            'phone' => $user->phone,
            'channel' => $channel,
            'code' => $plainCode,
        ]);

        return [
            'provider' => 'log',
            'channel' => $channel,
            'status' => 'logged',
            'to' => $user->phone_normalized ?: $user->phone,
            'message' => 'OTP_PROVIDER esta en log. No se envio SMS real; el codigo queda en storage/logs/laravel.log.',
        ];
    }
}
