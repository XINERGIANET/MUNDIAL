<x-guest-layout>
    <div class="min-h-screen bg-[#f6f8fb]">
        <header class="border-b border-white/20 wc-shell text-white">
            <div class="max-w-7xl mx-auto px-4 py-5 flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="grid h-11 w-11 place-items-center rounded-lg bg-white text-sm font-black text-blue-800">26</span>
                    <span class="font-black uppercase tracking-wide">Polla Mundialista</span>
                </a>
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
            <section class="max-w-5xl mx-auto px-4 py-10">
                {{-- Octavos con Posibles --}}
                @if ($octavos->isNotEmpty())
                    <div class="mt-10 mb-6">
                        <p class="text-sm font-black uppercase tracking-wide text-red-600">Fase eliminatoria</p>
                        <h2 class="text-2xl font-black text-gray-950">Octavos de Final</h2>
                        <p class="mt-1 text-sm text-gray-500">Partidos a pronosticar — horarios en hora de Lima (Perú).</p>
                    </div>

                    @foreach ($octavos->groupBy(fn ($m) => $m->starts_at->setTimezone('America/Lima')->format('Y-m-d')) as $day => $dayMatches)
                        <div class="mb-6">
                            <h3 class="mb-3 border-b border-gray-200 pb-2 text-xs font-black uppercase tracking-widest text-gray-400">
                                {{ $dayMatches->first()->starts_at->setTimezone('America/Lima')->locale('es')->isoFormat('dddd D [de] MMMM') }}
                            </h3>
                            <div class="grid gap-3">
                                @foreach ($dayMatches as $match)
                                    @php
                                        $homeSrc = $match->homeSourceMatch;
                                        $awaySrc = $match->awaySourceMatch;
                                    @endphp
                                    <div class="wc-card rounded-xl p-5">
                                        <div class="flex items-center gap-3">

                                            {{-- Posibles local --}}
                                            @if ($homeSrc)
                                                <div class="hidden shrink-0 flex-col items-center gap-1.5 sm:flex">
                                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-300">Posibles</p>
                                                    <div class="rounded-xl border border-dashed border-gray-200 bg-white px-4 py-3">
                                                        <div class="flex items-center gap-3">
                                                            <div class="flex items-center gap-2">
                                                                @if ($homeSrc->homeTeam?->logo_path)
                                                                    <img src="{{ $homeSrc->homeTeam->logo_path }}" alt="" class="h-6 w-9 shrink-0 rounded object-cover">
                                                                @endif
                                                                <span class="text-sm font-black leading-tight text-gray-800">{{ $homeSrc->homeTeam?->name ?? '?' }}</span>
                                                            </div>
                                                            <span class="shrink-0 text-xs font-bold text-gray-300">vs</span>
                                                            <div class="flex items-center gap-2">
                                                                @if ($homeSrc->awayTeam?->logo_path)
                                                                    <img src="{{ $homeSrc->awayTeam->logo_path }}" alt="" class="h-6 w-9 shrink-0 rounded object-cover">
                                                                @endif
                                                                <span class="text-sm font-black leading-tight text-gray-800">{{ $homeSrc->awayTeam?->name ?? '?' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Equipo local --}}
                                            <div class="flex flex-1 flex-col items-center gap-2 text-center">
                                                @if ($match->homeTeam?->logo_path && $match->homeTeam->is_active)
                                                    <img src="{{ $match->homeTeam->logo_path }}" alt="{{ $match->homeTeam->name }}"
                                                         class="h-14 w-20 rounded-lg object-contain ring-1 ring-gray-100">
                                                @else
                                                    <div class="flex h-14 w-20 items-center justify-center rounded-lg bg-gray-100 text-2xl text-gray-300 ring-1 ring-gray-200">?</div>
                                                @endif
                                                <div>
                                                    <p class="font-black leading-tight text-gray-950">{{ $match->homeTeam?->name ?? 'Por definir' }}</p>
                                                    <p class="text-xs font-bold uppercase tracking-wide text-gray-400">Local</p>
                                                </div>
                                            </div>

                                            {{-- Centro: hora --}}
                                            <div class="flex shrink-0 flex-col items-center gap-1">
                                                <div class="rounded-xl border-2 border-dashed border-gray-200 px-5 py-3 text-center">
                                                    <p class="text-sm font-black text-gray-300">VS</p>
                                                </div>
                                                <p class="text-xs font-bold text-blue-600">{{ $match->starts_at->setTimezone('America/Lima')->format('g:i A') }}</p>
                                            </div>

                                            {{-- Equipo visitante --}}
                                            <div class="flex flex-1 flex-col items-center gap-2 text-center">
                                                @if ($match->awayTeam?->logo_path && $match->awayTeam->is_active)
                                                    <img src="{{ $match->awayTeam->logo_path }}" alt="{{ $match->awayTeam->name }}"
                                                         class="h-14 w-20 rounded-lg object-contain ring-1 ring-gray-100">
                                                @else
                                                    <div class="flex h-14 w-20 items-center justify-center rounded-lg bg-gray-100 text-2xl text-gray-300 ring-1 ring-gray-200">?</div>
                                                @endif
                                                <div>
                                                    <p class="font-black leading-tight text-gray-950">{{ $match->awayTeam?->name ?? 'Por definir' }}</p>
                                                    <p class="text-xs font-bold uppercase tracking-wide text-gray-400">Visitante</p>
                                                </div>
                                            </div>

                                            {{-- Posibles visitante --}}
                                            @if ($awaySrc)
                                                <div class="hidden shrink-0 flex-col items-center gap-1.5 sm:flex">
                                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-300">Posibles</p>
                                                    <div class="rounded-xl border border-dashed border-gray-200 bg-white px-4 py-3">
                                                        <div class="flex items-center gap-3">
                                                            <div class="flex items-center gap-2">
                                                                @if ($awaySrc->homeTeam?->logo_path)
                                                                    <img src="{{ $awaySrc->homeTeam->logo_path }}" alt="" class="h-6 w-9 shrink-0 rounded object-cover">
                                                                @endif
                                                                <span class="text-sm font-black leading-tight text-gray-800">{{ $awaySrc->homeTeam?->name ?? '?' }}</span>
                                                            </div>
                                                            <span class="shrink-0 text-xs font-bold text-gray-300">vs</span>
                                                            <div class="flex items-center gap-2">
                                                                @if ($awaySrc->awayTeam?->logo_path)
                                                                    <img src="{{ $awaySrc->awayTeam->logo_path }}" alt="" class="h-6 w-9 shrink-0 rounded object-cover">
                                                                @endif
                                                                <span class="text-sm font-black leading-tight text-gray-800">{{ $awaySrc->awayTeam?->name ?? '?' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif

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
