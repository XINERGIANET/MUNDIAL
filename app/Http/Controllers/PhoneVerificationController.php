<?php

namespace App\Http\Controllers;

use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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

    public function resend(Request $request, OtpService $otpService): RedirectResponse
    {
        $otpService->issue($request->user(), config('polla.otp_channel_default'), $request->ip(), $request->userAgent());

        return back()->with('status', 'Enviamos un nuevo codigo.');
    }
}
