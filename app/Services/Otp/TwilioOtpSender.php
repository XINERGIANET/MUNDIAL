<?php

namespace App\Services\Otp;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class TwilioOtpSender
{
    public function sendSms(User $user, string $plainCode): void
    {
        $from = config('polla.twilio_sms_from');

        if (! $from) {
            throw new RuntimeException('TWILIO_SMS_FROM no esta configurado.');
        }

        $this->sendMessage($user, $plainCode, $from, $this->toE164($user));
    }

    public function sendWhatsApp(User $user, string $plainCode): void
    {
        $from = config('polla.twilio_whatsapp_from');

        if (! $from) {
            throw new RuntimeException('TWILIO_WHATSAPP_FROM no esta configurado.');
        }

        $this->sendMessage($user, $plainCode, $from, 'whatsapp:'.$this->toE164($user));
    }

    private function sendMessage(User $user, string $plainCode, string $from, string $to): void
    {
        $sid = config('polla.twilio_account_sid');
        $token = config('polla.twilio_auth_token');

        if (! $sid || ! $token) {
            throw new RuntimeException('Las credenciales de Twilio no estan configuradas.');
        }

        $response = Http::asForm()
            ->withBasicAuth($sid, $token)
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                'From' => $from,
                'To' => $to,
                'Body' => "Tu codigo de verificacion para ".config('app.name')." es {$plainCode}.",
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Twilio no pudo enviar el codigo: '.$response->body());
        }
    }

    private function toE164(User $user): string
    {
        $phone = preg_replace('/\D+/', '', $user->phone_normalized ?: $user->phone);
        $countryCode = preg_replace('/\D+/', '', (string) config('polla.otp_default_country_code'));

        if ($countryCode && ! str_starts_with($phone, $countryCode)) {
            $phone = $countryCode.$phone;
        }

        return '+'.$phone;
    }
}
