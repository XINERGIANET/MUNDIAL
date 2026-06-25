<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PhoneNormalizer;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request, PhoneNormalizer $normalizer): RedirectResponse
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'phone'     => ['required', 'string', 'max:30'],
            'email'     => ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'otp_channel' => ['required', 'in:sms,whatsapp'],
            'otp_code'  => ['required', 'string', 'digits:6'],
            'password'  => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $phone = $normalizer->normalize($data['phone']);

        $otp = session('pre_register_otp');

        if (! $otp || $otp['phone_normalized'] !== $phone) {
            throw ValidationException::withMessages([
                'otp_code' => 'El código no corresponde al número ingresado. Solicita uno nuevo.',
            ]);
        }

        if ($otp['expires_at'] < now()->timestamp) {
            session()->forget('pre_register_otp');
            throw ValidationException::withMessages([
                'otp_code' => 'El código ha expirado. Solicita uno nuevo.',
            ]);
        }

        $otp['attempts']++;
        session(['pre_register_otp' => $otp]);

        if ($otp['attempts'] > config('polla.otp_max_attempts', 5)) {
            session()->forget('pre_register_otp');
            throw ValidationException::withMessages([
                'otp_code' => 'Se superó el límite de intentos. Solicita un nuevo código.',
            ]);
        }

        if (! Hash::check($data['otp_code'], $otp['code_hash'])) {
            throw ValidationException::withMessages([
                'otp_code' => 'El código ingresado no es válido.',
            ]);
        }

        if (User::where('phone_normalized', $phone)->exists()) {
            throw ValidationException::withMessages([
                'phone' => 'Este número de celular ya está registrado.',
            ]);
        }

        session()->forget('pre_register_otp');

        $user = User::create([
            'name'             => $data['name'],
            'phone'            => $data['phone'],
            'phone_normalized' => $phone,
            'email'            => $data['email'] ?? null,
            'phone_verified_at' => now(),
            'password'         => Hash::make($data['password']),
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
