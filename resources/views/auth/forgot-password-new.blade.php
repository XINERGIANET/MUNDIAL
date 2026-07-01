<x-guest-layout>
    <p class="mb-5 text-sm text-gray-600">
        Identidad verificada. Elige una nueva contraseña para tu cuenta.
    </p>

    @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('password.phone.reset') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="password" value="Nueva contraseña" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required autofocus />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Confirmar contraseña" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center">
            Guardar nueva contraseña
        </x-primary-button>
    </form>
</x-guest-layout>
