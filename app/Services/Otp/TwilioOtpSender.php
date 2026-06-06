<?php

namespace App\Services\Otp;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class TwilioOtpSender
{
    public function sendSms(User $user, string $plainCode): array
    {
        $from = config('polla.twilio_sms_from');

        if (! $from) {
            throw new RuntimeException('TWILIO_SMS_FROM no esta configurado.');
        }

        return $this->sendMessage($user, $plainCode, $from, $this->toE164($user), 'sms');
    }

    public function sendWhatsApp(User $user, string $plainCode): array
    {
        $from = config('polla.twilio_whatsapp_from');

        if (! $from) {
            throw new RuntimeException('TWILIO_WHATSAPP_FROM no esta configurado.');
        }

        return $this->sendMessage($user, $plainCode, $from, 'whatsapp:'.$this->toE164($user), 'whatsapp');
    }

    private function sendMessage(User $user, string $plainCode, string $from, string $to, string $channel): array
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
            $code = $response->json('code');
            $message = $response->json('message') ?: $response->body();

            throw new RuntimeException('Twilio no pudo enviar el codigo'.($code ? " ({$code})" : '').": {$message}");
        }

        $result = [
            'provider' => 'twilio',
            'channel' => $channel,
            'sid' => $response->json('sid'),
            'status' => $response->json('status'),
            'to' => $response->json('to') ?: $to,
            'from' => $response->json('from') ?: $from,
            'price' => $response->json('price'),
            'price_unit' => $response->json('price_unit'),
            'error_code' => $response->json('error_code'),
            'error_message' => $response->json('error_message'),
            'date_created' => $response->json('date_created'),
            'date_sent' => $response->json('date_sent'),
            'uri' => $response->json('uri'),
        ];

        Log::info('Twilio OTP message accepted', [
            'user_id' => $user->id,
            ...$result,
        ]);

        return $result;
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
