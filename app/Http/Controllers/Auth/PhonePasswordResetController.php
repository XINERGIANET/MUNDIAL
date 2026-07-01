<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use App\Services\PhoneNormalizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PhonePasswordResetController extends Controller
{
    public function showPhoneForm(): View
    {
        return view('auth.forgot-password-phone');
    }

    public function sendOtp(Request $request, OtpService $otpService, PhoneNormalizer $normalizer): RedirectResponse
    {
        $data = $request->validate([
            'phone'       => ['required', 'string', 'max:30'],
            'otp_channel' => ['required', 'in:sms,whatsapp'],
        ]);

        $normalized = $normalizer->normalize($data['phone']);

        $user = User::where('phone_normalized', $normalized)
            ->orWhere('phone', $data['phone'])
            ->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'phone' => 'No encontramos una cuenta con ese número de celular.',
            ]);
        }

        try {
            $otpService->issueWithResult($user, $data['otp_channel'], $request->ip(), $request->userAgent());
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw ValidationException::withMessages([
                'phone' => 'No se pudo enviar el código: ' . $e->getMessage(),
            ]);
        }

        $request->session()->put('password_reset_user_id', $user->id);
        $request->session()->put('password_reset_channel', $data['otp_channel']);

        return redirect()->route('password.phone.verify');
    }

    public function showOtpForm(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('password_reset_user_id')) {
            return redirect()->route('password.request');
        }

        $user = User::find($request->session()->get('password_reset_user_id'));

        return view('auth.forgot-password-verify', [
            'maskedPhone' => $user?->maskedPhone() ?? '***',
        ]);
    }

    public function resendOtp(Request $request, OtpService $otpService): RedirectResponse
    {
        $userId = $request->session()->get('password_reset_user_id');

        if (! $userId) {
            return redirect()->route('password.request');
        }

        $data = $request->validate(['otp_channel' => ['required', 'in:sms,whatsapp']]);

        $user = User::findOrFail($userId);

        try {
            $otpService->issueWithResult($user, $data['otp_channel'], $request->ip(), $request->userAgent());
            $request->session()->put('password_reset_channel', $data['otp_channel']);
        } catch (ValidationException $e) {
            return redirect()->route('password.phone.verify')
                ->withErrors(['code' => collect($e->errors())->flatten()->first()]);
        } catch (Throwable $e) {
            return redirect()->route('password.phone.verify')
                ->withErrors(['code' => 'Error al reenviar: ' . $e->getMessage()]);
        }

        return redirect()->route('password.phone.verify')
            ->with('status', 'Código reenviado correctamente.');
    }

    public function verifyOtp(Request $request, OtpService $otpService): RedirectResponse
    {
        $userId = $request->session()->get('password_reset_user_id');

        if (! $userId) {
            return redirect()->route('password.request');
        }

        $data = $request->validate(['code' => ['required', 'digits:6']]);

        $user = User::findOrFail($userId);

        $otpService->verify($user, $data['code']);

        $request->session()->put('password_reset_verified', true);

        return redirect()->route('password.phone.new');
    }

    public function showNewPasswordForm(Request $request): View|RedirectResponse
    {
        if (! $request->session()->get('password_reset_verified')) {
            return redirect()->route('password.request');
        }

        return view('auth.forgot-password-new');
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        if (! $request->session()->get('password_reset_verified')) {
            return redirect()->route('password.request');
        }

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::findOrFail($request->session()->get('password_reset_user_id'));
        $user->forceFill(['password' => Hash::make($data['password'])])->save();

        $request->session()->forget(['password_reset_user_id', 'password_reset_verified', 'password_reset_channel']);

        return redirect()->route('login')
            ->with('status', 'Contraseña actualizada correctamente. Ya puedes iniciar sesión.');
    }
}
