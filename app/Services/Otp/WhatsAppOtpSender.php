<?php

namespace App\Services\Otp;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class WhatsAppOtpSender implements OtpSenderInterface
{
    public function send(User $user, string $plainCode, string $channel): void
    {
        Log::info('WhatsApp OTP provider placeholder', [
            'user_id' => $user->id,
            'phone' => $user->phone,
            'provider' => env('WHATSAPP_PROVIDER', 'meta_cloud_api'),
        ]);
    }
}
