<?php

namespace App\Services\Otp;

use App\Models\User;

class ChannelOtpSender implements OtpSenderInterface
{
    public function send(User $user, string $plainCode, string $channel): array
    {
        if (config('polla.otp_provider') === 'log') {
            return app(LogOtpSender::class)->send($user, $plainCode, $channel);
        }

        return match ($channel) {
            'whatsapp' => app(WhatsAppOtpSender::class)->send($user, $plainCode, $channel),
            default => app(SmsOtpSender::class)->send($user, $plainCode, $channel),
        };
    }
}
