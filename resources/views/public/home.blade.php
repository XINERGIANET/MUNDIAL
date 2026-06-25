<x-guest-layout>
    <div class="min-h-screen bg-[#f6f8fb]">
        <header class="border-b border-white/20 wc-shell text-white">
            <div class="max-w-7xl mx-auto px-4 py-5 flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="grid h-11 w-11 place-items-center rounded-lg bg-white text-sm font-black text-blue-800">26</span>
                    <span class="font-black uppercase tracking-wide">Polla Mundialista</span>
                </a>
                <div class="flex items-center gap-4 text-sm font-semibold">
                    <a href="{{ route('login') }}" class="text-white/85 hover:text-white">Iniciar sesión</a>
                    <a href="{{ route('register') }}" class="rounded-md bg-white px-4 py-2 text-blue-800 hover:bg-blue-50">Registrarse</a>
                </div>
            </div>
        </header>

        <main>
            {{-- Hero / CTA --}}
            <section class="wc-shell text-white">
                <div class="max-w-3xl mx-auto px-4 py-12 text-center">
                    <p class="text-xs font-black uppercase tracking-[.2em] text-white/70">Canada · Mexico · USA 2026</p>
                    <h1 class="mt-4 text-4xl font-black tracking-tight sm:text-5xl">Polla Mundialista 2026</h1>
                    <p class="mt-4 text-base text-white/80">Pronostica los partidos de la eliminatoria y compite en el ranking con tus amigos.</p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                        <a href="{{ route('register') }}" class="rounded-md bg-white px-8 py-3 text-center text-sm font-black text-blue-800 hover:bg-blue-50">
                            Inscríbete ahora
                        </a>
                        <a href="{{ route('login') }}" class="rounded-md border border-white/30 px-8 py-3 text-center text-sm font-black text-white hover:bg-white/10">
                            Ya tengo cuenta
                        </a>
                    </div>
                </div>
            </section>

            {{-- Match schedule --}}
            <section class="max-w-3xl mx-auto px-4 py-10">
                <div class="mb-6">
                    <p class="text-sm font-black uppercase tracking-wide text-red-600">Fase eliminatoria</p>
                    <h2 class="text-2xl font-black text-gray-950">Dieciseisavos de Final</h2>
                    <p class="mt-1 text-sm text-gray-500">Horarios en hora de Lima (Perú).</p>
                </div>

                @forelse ($matches->groupBy(fn ($m) => $m->starts_at->setTimezone('America/Lima')->format('Y-m-d')) as $day => $dayMatches)
                    <div class="mb-8">
                        <h3 class="mb-3 text-xs font-black uppercase tracking-widest text-gray-400 border-b border-gray-200 pb-2">
                            {{ $dayMatches->first()->starts_at->setTimezone('America/Lima')->locale('es')->isoFormat('dddd D [de] MMMM') }}
                        </h3>
                        <div class="grid gap-2">
                            @foreach ($dayMatches as $match)
                                <div class="wc-card rounded-lg p-4 flex items-center gap-3">

                                    {{-- Home team --}}
                                    <div class="flex flex-1 items-center justify-end gap-3">
                                        @if ($match->homeTeam->is_active)
                                            <span class="text-right text-sm font-black text-gray-900 leading-tight">{{ $match->homeTeam->name }}</span>
                                            <img src="{{ $match->homeTeam->logo_path }}"
                                                 alt="{{ $match->homeTeam->name }}"
                                                 class="h-8 w-12 object-contain rounded-sm flex-shrink-0">
                                        @else
                                            <span class="text-right text-sm font-semibold text-gray-400 leading-tight">A definir</span>
                                            <span class="h-8 w-12 flex-shrink-0 rounded-sm bg-gray-100 flex items-center justify-center text-gray-300 text-xs">?</span>
                                        @endif
                                    </div>

                                    {{-- Time --}}
                                    <div class="flex flex-col items-center flex-shrink-0 w-16">
                                        <span class="text-sm font-black text-blue-700">{{ $match->starts_at->setTimezone('America/Lima')->format('g:i A') }}</span>
                                    </div>

                                    {{-- Away team --}}
                                    <div class="flex flex-1 items-center gap-3">
                                        @if ($match->awayTeam->is_active)
                                            <img src="{{ $match->awayTeam->logo_path }}"
                                                 alt="{{ $match->awayTeam->name }}"
                                                 class="h-8 w-12 object-contain rounded-sm flex-shrink-0">
                                            <span class="text-sm font-black text-gray-900 leading-tight">{{ $match->awayTeam->name }}</span>
                                        @else
                                            <span class="h-8 w-12 flex-shrink-0 rounded-sm bg-gray-100 flex items-center justify-center text-gray-300 text-xs">?</span>
                                            <span class="text-sm font-semibold text-gray-400 leading-tight">A definir</span>
                                        @endif
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Los partidos se publicarán pronto.</p>
                @endforelse

                {{-- Bottom CTA --}}
                <div class="mt-6 rounded-lg wc-shell p-6 text-center text-white">
                    <p class="font-black text-lg">¿Listo para competir?</p>
                    <p class="mt-1 text-sm text-white/80">Regístrate e ingresa tus pronósticos antes del cierre de cada partido.</p>
                    <a href="{{ route('register') }}" class="mt-4 inline-block rounded-md bg-white px-8 py-3 text-sm font-black text-blue-800 hover:bg-blue-50">
                        Inscribirme ahora
                    </a>
                </div>
            </section>
        </main>
    </div>
</x-guest-layout>
