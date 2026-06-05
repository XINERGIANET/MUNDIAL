<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
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
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request, PhoneNormalizer $normalizer, OtpService $otpService): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'otp_channel' => ['required', 'in:sms,whatsapp'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $phone = $normalizer->normalize($data['phone']);

        if (User::where('phone_normalized', $phone)->exists()) {
            throw ValidationException::withMessages(['phone' => 'Este numero de celular ya esta registrado.']);
        }

        $user = User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'phone_normalized' => $phone,
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        $otpService->issue($user, $data['otp_channel'], $request->ip(), $request->userAgent());

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('phone.verify', absolute: false));
    }
}
