<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Otp\ChannelOtpSender;
use App\Services\PhoneNormalizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PreRegistrationOtpController extends Controller
{
    public function send(Request $request, PhoneNormalizer $normalizer, ChannelOtpSender $sender): JsonResponse
    {
        $request->validate([
            'phone'       => ['required', 'string', 'max:30'],
            'otp_channel' => ['required', 'in:sms,whatsapp'],
        ]);

        $phone = $normalizer->normalize($request->phone);

        if (User::where('phone_normalized', $phone)->exists()) {
            return response()->json(['message' => 'Este número de celular ya está registrado.'], 422);
        }

        $code = (string) random_int(100000, 999999);

        session(['pre_register_otp' => [
            'phone_normalized' => $phone,
            'phone_raw'        => $request->phone,
            'channel'          => $request->otp_channel,
            'code_hash'        => Hash::make($code),
            'expires_at'       => now()->addMinutes(config('polla.otp_expires_minutes', 10))->timestamp,
            'attempts'         => 0,
        ]]);

        $tempUser = new User([
            'phone'            => $request->phone,
            'phone_normalized' => $phone,
        ]);

        $sender->send($tempUser, $code, $request->otp_channel);

        return response()->json(['message' => 'Código enviado correctamente.']);
    }
}
