<x-guest-layout>
    @php
        $showStep2 = $errors->hasAny(['otp_code', 'name', 'email', 'password', 'password_confirmation']);
    @endphp

    <script>
        window._sendCodeUrl = "{{ route('register.send-code') }}";
    </script>

    <div
        x-data="{
            step: {{ $showStep2 ? 2 : 1 }},
            sending: false,
            error: '',
            showPassword: false,
            showPasswordConfirmation: false,
            password: '',
            passwordConfirmation: '',
            async sendCode() {
                this.sending = true;
                this.error = '';
                try {
                    const res = await fetch(window._sendCodeUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            phone: this.$refs.phone.value,
                            otp_channel: 'sms',
                        })
                    });
                    const json = await res.json();
                    if (!res.ok) {
                        this.error = json.message || (json.errors && Object.values(json.errors)[0]?.[0]) || 'Error al enviar el código.';
                    } else {
                        this.step = 2;
                        this.$nextTick(() => this.$refs.otp_code && this.$refs.otp_code.focus());
                    }
                } catch (e) {
                    this.error = 'Error de conexión. Intenta de nuevo.';
                } finally {
                    this.sending = false;
                }
            }
        }"
    >
        <form method="POST" action="{{ route('register') }}"
              @submit="if (password !== passwordConfirmation) { $event.preventDefault(); $refs.passwordConfirmation.setCustomValidity('Las contraseñas no coinciden.'); $refs.passwordConfirmation.reportValidity(); }">
            @csrf

            {{-- Canal fijo: SMS --}}
            <input type="hidden" name="otp_channel" value="sms" />

            {{-- Celular --}}
            <div>
                <x-input-label for="phone" value="Celular" />
                <div :class="step === 2 ? 'opacity-60 pointer-events-none' : ''">
                    <x-text-input
                        x-ref="phone"
                        id="phone"
                        class="block mt-1 w-full"
                        type="tel"
                        name="phone"
                        :value="old('phone')"
                        required
                        autocomplete="tel"
                        placeholder="Ej: 987654321"
                    />
                </div>
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            {{-- Paso 1: botón enviar código --}}
            <div x-show="step === 1" class="mt-5">
                <p class="text-sm text-gray-500 mb-3">Se enviará un SMS con el código de verificación a su número de celular.</p>
                <p x-show="error" x-text="error" class="text-red-600 text-sm mb-3"></p>
                <button
                    type="button"
                    @click="sendCode()"
                    :disabled="sending"
                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 disabled:opacity-50 transition"
                >
                    <span x-show="!sending">Enviar código de verificación</span>
                    <span x-show="sending">Enviando...</span>
                </button>
                <div class="mt-4 text-center">
                    <a class="text-sm text-gray-600 underline hover:text-gray-900" href="{{ route('login') }}">Ya tengo cuenta</a>
                </div>
            </div>

            {{-- Paso 2: código + formulario completo --}}
            <div x-show="step === 2" x-cloak>

                <div class="mt-5">
                    <x-input-label for="otp_code" value="Código de verificación" />
                    <x-text-input
                        x-ref="otp_code"
                        id="otp_code"
                        class="block mt-1 w-full tracking-[0.4em] text-center font-mono text-lg"
                        type="text"
                        name="otp_code"
                        :value="old('otp_code')"
                        required
                        maxlength="6"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        placeholder="● ● ● ● ● ●"
                    />
                    <p class="mt-1 text-xs text-gray-500">Ingresa el código de 6 dígitos que recibiste por SMS.</p>
                    <x-input-error :messages="$errors->get('otp_code')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="name" value="Nombre completo" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="password" value="Contraseña" />
                    <div class="relative mt-1">
                        <x-text-input id="password" class="block w-full pr-11" x-model="password" x-bind:type="showPassword ? 'text' : 'password'" @input="$nextTick(() => $refs.passwordConfirmation.setCustomValidity(password === passwordConfirmation ? '' : 'Las contraseñas no coinciden.'))" name="password" required autocomplete="new-password" />
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-gray-500 hover:text-gray-700" :aria-label="showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'">
                            <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M10.585 10.587a2 2 0 002.828 2.828M9.878 4.242A9.7 9.7 0 0112 4c4.478 0 8.268 2.943 9.542 7a9.77 9.77 0 01-1.193 2.487M6.61 6.61C4.83 7.8 3.432 9.68 2.458 12c1.274 4.057 5.065 7 9.542 7 1.52 0 2.956-.34 4.232-.947"/></svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="password_confirmation" value="Confirmar contraseña" />
                    <div class="relative mt-1">
                        <x-text-input x-ref="passwordConfirmation" id="password_confirmation" class="block w-full pr-11" x-model="passwordConfirmation" x-bind:type="showPasswordConfirmation ? 'text' : 'password'" @input="$el.setCustomValidity(password === passwordConfirmation ? '' : 'Las contraseñas no coinciden.')" name="password_confirmation" required autocomplete="new-password" />
                        <button type="button" @click="showPasswordConfirmation = !showPasswordConfirmation" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-gray-500 hover:text-gray-700" :aria-label="showPasswordConfirmation ? 'Ocultar contraseña' : 'Mostrar contraseña'">
                            <svg x-show="!showPasswordConfirmation" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="showPasswordConfirmation" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M10.585 10.587a2 2 0 002.828 2.828M9.878 4.242A9.7 9.7 0 0112 4c4.478 0 8.268 2.943 9.542 7a9.77 9.77 0 01-1.193 2.487M6.61 6.61C4.83 7.8 3.432 9.68 2.458 12c1.274 4.057 5.065 7 9.542 7 1.52 0 2.956-.34 4.232-.947"/></svg>
                        </button>
                    </div>
                    <p x-show="passwordConfirmation && password !== passwordConfirmation" x-cloak class="mt-2 text-sm text-red-600">Las contraseñas no coinciden.</p>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex justify-end mt-5">
                    <x-primary-button>Registrarme</x-primary-button>
                </div>
            </div>

        </form>
    </div>
</x-guest-layout>
