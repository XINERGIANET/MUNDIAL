<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Verifica tu celular</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-lg mx-auto px-4">
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                <p class="text-sm text-gray-600 mb-4">Ingresa el codigo de 6 digitos enviado a {{ auth()->user()->maskedPhone() }}.</p>

                @if (session('status'))
                    <div class="mb-4 rounded-md bg-green-50 p-3 text-sm text-green-700">{{ session('status') }}</div>
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

                <form method="POST" action="{{ route('phone.verify.resend') }}" class="mt-4">
                    @csrf
                    <button class="text-sm font-medium text-blue-700 hover:text-blue-900">Reenviar codigo</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
