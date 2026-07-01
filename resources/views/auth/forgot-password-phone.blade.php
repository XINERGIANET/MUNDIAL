<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <p class="mb-5 text-sm text-gray-600">
        Ingresa tu número de celular y te enviaremos un código para restablecer tu contraseña.
    </p>

    @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('password.phone.send') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="phone" value="Número de celular" />
            <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" :value="old('phone')" required autofocus placeholder="Ej: 999123456" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="otp_channel" value="Enviar código por" />
            <select id="otp_channel" name="otp_channel"
                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="sms" @selected(old('otp_channel', config('polla.otp_channel_default')) === 'sms')>SMS</option>
                <option value="whatsapp" @selected(old('otp_channel', config('polla.otp_channel_default')) === 'whatsapp')>WhatsApp</option>
            </select>
            <x-input-error :messages="$errors->get('otp_channel')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end gap-3 pt-1">
            <a href="{{ route('login') }}" class="text-sm text-gray-600 underline hover:text-gray-900">
                Volver
            </a>
            <x-primary-button>Enviar código</x-primary-button>
        </div>
    </form>
</x-guest-layout>
