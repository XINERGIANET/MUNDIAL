<x-guest-layout>
    <div class="min-h-screen bg-[#f6f8fb]">
        <header class="border-b border-white/20 wc-shell text-white">
            <div class="max-w-7xl mx-auto px-4 py-5 flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="grid h-11 w-11 place-items-center rounded-lg bg-white text-sm font-black text-blue-800">26</span>
                    <span class="font-black uppercase tracking-wide">Polla Mundialista</span>
                </a>
                <a href="{{ route('login') }}" class="rounded-md border border-white/30 px-5 py-2 text-sm font-black text-white hover:bg-white/10">
                    Iniciar sesión
                </a>
            </div>
        </header>

        <main>
            {{-- Hero / CTA --}}
            <section class="wc-shell text-white">
                <div class="max-w-3xl mx-auto px-4 py-12 text-center">
                    <p class="text-xs font-black uppercase tracking-[.2em] text-white/70">Canada · Mexico · USA 2026</p>
                    <h1 class="mt-4 text-4xl font-black tracking-tight sm:text-5xl">Polla Mundialista 2026</h1>
                    <p class="mt-4 text-base leading-relaxed text-white/80">
                        Desde octavos de final, vive el mundial con mayor emoción.<br>
                        Reta a tus amigos y conviértete en el campeón de la Polla Mundialista de Xinergia.
                    </p>

                    {{-- Inscripciones + pozo --}}
                    <div class="mt-6 flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
                        <div class="rounded-full bg-white/10 px-5 py-2 text-sm font-bold text-white/90 ring-1 ring-white/20">
                            📅 Inscripciones abiertas hasta el <span class="font-black text-white">4 de julio · 11:00 am</span>
                        </div>
                    </div>

                    {{-- Pozo --}}
                    <div class="mt-4 inline-flex items-center gap-3 rounded-2xl bg-white/10 px-6 py-4 ring-1 ring-white/20">
                        <div class="text-left">
                            <p class="text-xs font-black uppercase tracking-widest text-white/60">Pozo de premios en aumento</p>
                            <p class="mt-0.5 text-sm text-white/80">Cada nuevo participante incrementa el premio</p>
                        </div>
                        <div class="shrink-0 rounded-xl bg-white px-4 py-2 text-center">
                            <p class="text-2xl font-black text-blue-800">{{ $participantCount }}</p>
                            <p class="text-[10px] font-black uppercase tracking-wide text-blue-600">participantes</p>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-center">
                        <a href="{{ route('register') }}" class="rounded-xl bg-white px-12 py-4 text-center text-base font-black text-blue-800 shadow-lg hover:bg-blue-50">
                            Inscríbete ahora
                        </a>
                    </div>
                </div>
            </section>

            {{-- Cómo participar --}}
            <section class="max-w-5xl mx-auto px-4 pt-10">
                <div class="grid gap-4 sm:grid-cols-2">

                    {{-- Tarjeta izquierda: jugadas --}}
                    <div class="rounded-2xl border border-gray-200 bg-white p-6">
                        <h2 class="text-xl font-black text-gray-950">¿Cómo participas?</h2>
                        <p class="mt-2 text-sm leading-relaxed text-gray-600">
                            Con el pago de <span class="font-black text-gray-950">S/ 15</span>, obtienes <span class="font-black text-gray-950">1 jugada</span>, que te permite registrar tus pronósticos para todos los partidos de la fase eliminatoria:
                        </p>
                        <ul class="mt-4 space-y-2 text-sm text-gray-700">
                            <li class="flex items-center gap-2">⚽ <span><span class="font-black">Octavos de final</span> — 8 partidos</span></li>
                            <li class="flex items-center gap-2">⚽ <span><span class="font-black">Cuartos de final</span> — 4 partidos</span></li>
                            <li class="flex items-center gap-2">⚽ <span><span class="font-black">Semifinales</span> — 2 partidos</span></li>
                            <li class="flex items-center gap-2">⚽ <span><span class="font-black">Final</span> — 1 partido</span></li>
                            <li class="flex items-center gap-2">⚽ <span><span class="font-black">Partido por el tercer puesto</span> — 1 partido</span></li>
                        </ul>
                        <p class="mt-4 text-sm font-black text-gray-950">Total: 16 pronósticos.</p>
                    </div>

                    {{-- Tarjeta derecha: reglas --}}
                    <div class="rounded-2xl border border-gray-200 bg-white p-6">
                        <h2 class="text-xl font-black text-gray-950">Reglas de juego</h2>
                        <div class="mt-4 space-y-4 text-sm leading-relaxed text-gray-600">
                            <div class="flex gap-3">
                                <span class="mt-0.5 shrink-0 text-base">⏱️</span>
                                <p>Podrás colocar tu resultado <span class="font-bold text-gray-800">hasta 10 minutos antes del inicio de cada partido</span>. Al cerrarse el tiempo, podrás visualizar los resultados de cada usuario para garantizar la confiabilidad de la competencia.</p>
                            </div>
                            <div class="flex gap-3">
                                <span class="mt-0.5 shrink-0 text-base">🔄</span>
                                <p>En caso de empate a los 90 minutos, se tomará el resultado final obtenido en los 120 minutos, es decir, incluido el tiempo extra.</p>
                            </div>
                        </div>
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
                                                <div class="hidden w-56 shrink-0 sm:block">
                                                    <div class="rounded-xl border border-dashed border-gray-200 bg-white px-4 py-3">
                                                        <p class="mb-2 text-center text-[9px] font-black uppercase tracking-widest text-gray-300">Posibles</p>
                                                        <div class="flex flex-col items-center gap-1.5">
                                                            <div class="flex items-center gap-2">
                                                                @if ($homeSrc->homeTeam?->logo_path)
                                                                    <img src="{{ $homeSrc->homeTeam->logo_path }}" alt="" class="h-6 w-9 shrink-0 rounded object-cover">
                                                                @endif
                                                                <span class="text-sm font-black leading-tight text-gray-800">{{ $homeSrc->homeTeam?->name ?? '?' }}</span>
                                                            </div>
                                                            <span class="text-xs font-bold text-gray-300">vs</span>
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
                                                <div class="hidden w-56 shrink-0 sm:block">
                                                    <div class="rounded-xl border border-dashed border-gray-200 bg-white px-4 py-3">
                                                        <p class="mb-2 text-center text-[9px] font-black uppercase tracking-widest text-gray-300">Posibles</p>
                                                        <div class="flex flex-col items-center gap-1.5">
                                                            <div class="flex items-center gap-2">
                                                                @if ($awaySrc->homeTeam?->logo_path)
                                                                    <img src="{{ $awaySrc->homeTeam->logo_path }}" alt="" class="h-6 w-9 shrink-0 rounded object-cover">
                                                                @endif
                                                                <span class="text-sm font-black leading-tight text-gray-800">{{ $awaySrc->homeTeam?->name ?? '?' }}</span>
                                                            </div>
                                                            <span class="text-xs font-bold text-gray-300">vs</span>
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
