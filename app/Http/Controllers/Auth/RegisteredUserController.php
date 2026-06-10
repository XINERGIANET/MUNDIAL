<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use App\Services\PhoneNormalizer;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request, PhoneNormalizer $normalizer, OtpService $otpService): RedirectResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'phone'       => ['required', 'string', 'max:30'],
            'email'       => ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'otp_channel' => ['required', 'in:sms,whatsapp'],
            'password'    => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $phone       = $normalizer->normalize($data['phone']);
        $rawDigits   = preg_replace('/\D+/', '', $data['phone']);
        $countryCode = config('polla.otp_default_country_code', '51');

        // Covers: new normalization (992042725), old normalization (51992042725), raw digits with country code
        $normalizedVariants = array_unique([$phone, $rawDigits, $countryCode.$phone]);

        if (User::whereIn('phone_normalized', $normalizedVariants)->orWhere('phone', $data['phone'])->exists()) {
            throw ValidationException::withMessages(['phone' => 'Este numero de celular ya esta registrado.']);
        }

        try {
            $user = DB::transaction(function () use ($data, $phone, $otpService, $request) {
                $user = User::create([
                    'name'             => $data['name'],
                    'phone'            => $data['phone'],
                    'phone_normalized' => $phone,
                    'email'            => $data['email'] ?? null,
                    'password'         => Hash::make($data['password']),
                ]);

                $otpService->issue($user, $data['otp_channel'], $request->ip(), $request->userAgent());

                return $user;
            });
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                throw ValidationException::withMessages(['phone' => 'Este numero de celular ya esta registrado.']);
            }
            throw ValidationException::withMessages(['otp_channel' => $e->getMessage()]);
        } catch (Throwable $exception) {
            throw ValidationException::withMessages(['otp_channel' => $exception->getMessage()]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('phone.verify', absolute: false));
    }
}
