<?php

namespace App\Services;

use App\Jobs\SendOtpCodeJob;
use App\Models\PhoneVerificationCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class OtpService
{
    public function issue(User $user, string $channel, ?string $ip = null, ?string $userAgent = null): PhoneVerificationCode
    {
        $latest = $user->phoneVerificationCodes()->latest()->first();

        if ($latest && $latest->created_at->gt(now()->subSeconds(config('polla.otp_resend_seconds')))) {
            throw ValidationException::withMessages(['code' => 'Debes esperar antes de reenviar otro codigo.']);
        }

        $code = (string) random_int(100000, 999999);

        $verification = PhoneVerificationCode::create([
            'user_id' => $user->id,
            'phone' => $user->phone_normalized,
            'code_hash' => Hash::make($code),
            'channel' => $channel,
            'expires_at' => now()->addMinutes(config('polla.otp_expires_minutes')),
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ]);

        SendOtpCodeJob::dispatch($user, $code, $channel);

        return $verification;
    }

    public function verify(User $user, string $code): void
    {
        $verification = $user->phoneVerificationCodes()
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (! $verification) {
            throw ValidationException::withMessages(['code' => 'No hay un codigo pendiente de verificacion.']);
        }

        if ($verification->expires_at->isPast()) {
            throw ValidationException::withMessages(['code' => 'El codigo expiro. Solicita uno nuevo.']);
        }

        if ($verification->attempts >= config('polla.otp_max_attempts')) {
            throw ValidationException::withMessages(['code' => 'Se supero el limite de intentos. Solicita un nuevo codigo.']);
        }

        $verification->increment('attempts');

        if (! Hash::check($code, $verification->code_hash)) {
            throw ValidationException::withMessages(['code' => 'El codigo ingresado no es valido.']);
        }

        $verification->forceFill(['verified_at' => now()])->save();
        $user->forceFill(['phone_verified_at' => now()])->save();
    }
}
