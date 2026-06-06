<?php

namespace App\Http\Controllers;

use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Throwable;

class PhoneVerificationController extends Controller
{
    public function show(): View|RedirectResponse
    {
        if (auth()->user()->hasVerifiedPhone()) {
            return redirect()->route('dashboard');
        }

        return view('auth.verify-phone');
    }

    public function store(Request $request, OtpService $otpService): RedirectResponse
    {
        $data = $request->validate(['code' => ['required', 'digits:6']]);
        $otpService->verify($request->user(), $data['code']);

        return redirect()->route('dashboard')->with('status', 'Celular verificado correctamente.');
    }

    public function resend(Request $request, OtpService $otpService): View|JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'otp_channel' => ['required', 'in:sms,whatsapp'],
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ], 422);
            }

            return view('auth.verify-phone', [
                'sendError' => $validator->errors()->first(),
                'selectedOtpChannel' => config('polla.otp_channel_default'),
            ]);
        }

        $data = $validator->validated();

        try {
            $result = $otpService->issueWithResult($request->user(), $data['otp_channel'], $request->ip(), $request->userAgent());
            $delivery = $result['delivery'];
            $message = ($delivery['provider'] ?? null) === 'twilio'
                ? 'Twilio acepto la solicitud por '.($data['otp_channel'] === 'sms' ? 'SMS' : 'WhatsApp').' con estado '.($delivery['status'] ?? 'desconocido').'. Esto no confirma entrega al celular.'
                : (($delivery['provider'] ?? null) === 'log'
                    ? 'No se envio SMS real porque OTP_PROVIDER esta en log. Revisa storage/logs/laravel.log.'
                    : ($delivery['message'] ?? 'Enviamos un nuevo codigo por '.($data['otp_channel'] === 'sms' ? 'SMS.' : 'WhatsApp.')));

            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => true,
                    'message' => $message,
                    'channel' => $data['otp_channel'],
                    'verification' => [
                        'id' => $result['verification']->id,
                        'expires_at' => $result['verification']->expires_at?->toIso8601String(),
                    ],
                    'delivery' => $delivery,
                ]);
            }

            return view('auth.verify-phone', [
                'status' => $message,
                'twilioResult' => $delivery,
                'selectedOtpChannel' => $data['otp_channel'],
            ]);
        } catch (ValidationException $exception) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => collect($exception->errors())->flatten()->first(),
                    'errors' => $exception->errors(),
                ], 422);
            }

            return view('auth.verify-phone', [
                'sendError' => collect($exception->errors())->flatten()->first(),
                'selectedOtpChannel' => $data['otp_channel'],
            ]);
        } catch (Throwable $exception) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => $exception->getMessage(),
                    'error' => [
                        'type' => $exception::class,
                        'message' => $exception->getMessage(),
                    ],
                ], 500);
            }

            return view('auth.verify-phone', [
                'sendError' => $exception->getMessage(),
                'selectedOtpChannel' => $data['otp_channel'],
            ]);
        }
    }
}
