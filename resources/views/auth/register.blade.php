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
        <form method="POST" action="{{ route('register') }}">
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
                    <x-input-label for="email" value="Correo electrónico (opcional)" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" autocomplete="email" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="password" value="Contraseña" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="password_confirmation" value="Confirmar contraseña" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between mt-5">
                    <button type="button" @click="step = 1; error = ''" class="text-sm text-gray-500 underline hover:text-gray-700">
                        Cambiar número
                    </button>
                    <div class="flex items-center gap-3">
                        <a class="text-sm text-gray-600 underline hover:text-gray-900" href="{{ route('login') }}">Ya tengo cuenta</a>
                        <x-primary-button>Registrarme</x-primary-button>
                    </div>
                </div>
            </div>

        </form>
    </div>
</x-guest-layout>