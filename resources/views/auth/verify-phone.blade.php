<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Verifica tu celular</h2>
    </x-slot>

    @php
        $selectedOtpChannel = $selectedOtpChannel ?? old('otp_channel', config('polla.otp_channel_default'));
    @endphp

    <div class="py-8">
        <div class="max-w-lg mx-auto px-4">
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                <p class="text-sm text-gray-600 mb-4">Ingresa el codigo de 6 digitos enviado a {{ auth()->user()->maskedPhone() }}.</p>

                @if (session('status') || isset($status))
                    <div class="mb-4 rounded-md bg-green-50 p-3 text-sm text-green-700">{{ session('status') ?? $status }}</div>
                @endif

                @if (isset($sendError))
                    <div class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-700">{{ $sendError }}</div>
                @elseif ($errors->any())
                    <div class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('phone.verify.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="code" value="Codigo" />
                        <x-text-input id="code" name="code" inputmode="numeric" maxlength="6" class="mt-1 block w-full text-center text-xl tracking-widest" required />
                        <x-input-error :messages="$errors->get('code')" class="mt-2" />
                    </div>
                    <x-primary-button class="w-full justify-center">Verificar</x-primary-button>
                </form>

                <form method="POST" action="{{ route('phone.verify.resend') }}" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <x-input-label for="otp_channel" value="Metodo de verificacion" />
                        <select id="otp_channel" name="otp_channel" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="sms" @selected($selectedOtpChannel === 'sms')>SMS</option>
                            <option value="whatsapp" @selected($selectedOtpChannel === 'whatsapp')>WhatsApp</option>
                        </select>
                    </div>
                    <button class="text-sm font-medium text-blue-700 hover:text-blue-900">Reenviar codigo</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
