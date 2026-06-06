<?php

namespace App\Http\Controllers;

use App\Services\OtpService;
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

    public function resend(Request $request, OtpService $otpService): View
    {
        $validator = Validator::make($request->all(), [
            'otp_channel' => ['required', 'in:sms,whatsapp'],
        ]);

        if ($validator->fails()) {
            return view('auth.verify-phone', [
                'sendError' => $validator->errors()->first(),
                'selectedOtpChannel' => config('polla.otp_channel_default'),
            ]);
        }

        $data = $validator->validated();

        try {
            $otpService->issue($request->user(), $data['otp_channel'], $request->ip(), $request->userAgent());

            return view('auth.verify-phone', [
                'status' => 'Enviamos un nuevo codigo por '.($data['otp_channel'] === 'sms' ? 'SMS.' : 'WhatsApp.'),
                'selectedOtpChannel' => $data['otp_channel'],
            ]);
        } catch (ValidationException $exception) {
            return view('auth.verify-phone', [
                'sendError' => collect($exception->errors())->flatten()->first(),
                'selectedOtpChannel' => $data['otp_channel'],
            ]);
        } catch (Throwable $exception) {
            return view('auth.verify-phone', [
                'sendError' => $exception->getMessage(),
                'selectedOtpChannel' => $data['otp_channel'],
            ]);
        }
    }
}
