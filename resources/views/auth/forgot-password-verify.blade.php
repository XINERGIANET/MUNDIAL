<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <p class="mb-5 text-sm text-gray-600">
        Ingresa el código de 6 dígitos enviado a <span class="font-bold text-gray-800">{{ $maskedPhone }}</span>.
    </p>

    @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('password.phone.verify') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="code" value="Código de verificación" />
            <x-text-input
                id="code"
                name="code"
                inputmode="numeric"
                maxlength="6"
                class="mt-1 block w-full text-center text-2xl tracking-[.5em]"
                required
                autofocus
            />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center">
            Verificar código
        </x-primary-button>
    </form>

    {{-- Reenviar --}}
    <div class="mt-5 border-t border-gray-100 pt-4">
        <p class="mb-2 text-xs text-gray-500">¿No recibiste el código?</p>
        <form method="POST" action="{{ route('password.phone.resend') }}" class="flex items-center gap-3">
            @csrf
            <select name="otp_channel"
                class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="sms">SMS</option>
                <option value="whatsapp">WhatsApp</option>
            </select>
            <button type="submit" class="text-sm font-medium text-blue-700 hover:text-blue-900">
                Reenviar código
            </button>
        </form>
    </div>
</x-guest-layout>
