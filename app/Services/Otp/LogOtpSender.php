<?php

namespace App\Services\Otp;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class LogOtpSender implements OtpSenderInterface
{
    public function send(User $user, string $plainCode, string $channel): void
    {
        Log::info('OTP verification code generated', [
            'user_id' => $user->id,
            'phone' => $user->phone,
            'channel' => $channel,
            'code' => $plainCode,
        ]);
    }
}
