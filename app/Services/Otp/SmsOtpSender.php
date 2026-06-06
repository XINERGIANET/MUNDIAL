<?php

namespace App\Services\Otp;

use App\Models\User;

class SmsOtpSender implements OtpSenderInterface
{
    public function send(User $user, string $plainCode, string $channel): array
    {
        return app(TwilioOtpSender::class)->sendSms($user, $plainCode);
    }
}
