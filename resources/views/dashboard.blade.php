<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-blue-700">Centro de pronosticos</p>
                <h1 class="text-2xl font-black text-gray-950">Hola, {{ auth()->user()->name }}</h1>
            </div>
            <div class="inline-flex items-center gap-2 rounded-full bg-green-50 px-4 py-2 text-sm font-semibold text-green-700">
                <span class="h-2 w-2 rounded-full bg-green-500"></span>
                Celular verificado {{ auth()->user()->maskedPhone() }}
            </div>
        </div>
    </x-slot>

    <div class="pb-12">
        <section class="wc-shell">
            <div class="w-full px-4 py-8 sm:px-6 lg:px-8">
                <div class="grid gap-4 md:grid-cols-4">
                    <div class="rounded-lg bg-white/10 p-5 text-white ring-1 ring-white/20">
                        <p class="text-sm text-white/75">Torneos aprobados</p>
                        <p class="mt-2 text-3xl font-black">{{ $participants->where('status', 'approved')->count() }}</p>
                    </div>
                    <div class="rounded-lg bg-white/10 p-5 text-white ring-1 ring-white/20">
                        <p class="text-sm text-white/75">Pendientes de pago</p>
                        <p class="mt-2 text-3xl font-black">{{ $participants->where('status', 'pending_payment')->count() }}</p>
                    </div>
                    <div class="rounded-lg bg-white/10 p-5 text-white ring-1 ring-white/20">
                        <p class="text-sm text-white/75">Partidos abiertos</p>
                        <p class="mt-2 text-3xl font-black">{{ $upcomingMatches->count() }}</p>
                    </div>
                    <div class="rounded-lg bg-white/10 p-5 text-white ring-1 ring-white/20">
                        <p class="text-sm text-white/75">Mejor posicion</p>
                        <p class="mt-2 text-3xl font-black">{{ $rankings->min('position') ? '#'.$rankings->min('position') : '-' }}</p>
                    </div>
                </div>
            </div>
        </section>

        <div class="w-full px-4 py-8 sm:px-6 lg:px-8 space-y-8">
            @if (session('status'))
                <div class="rounded-lg bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-200">{{ session('status') }}</div>
            @endif

            <section class="grid gap-6 xl:grid-cols-[1fr_420px]">
                <div>
                    <div class="mb-4 flex items-end justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wide text-red-600">Jornada</p>
                            <h2 class="text-xl font-black text-gray-950">Partidos para pronosticar</h2>
                        </div>
                    </div>

                    <div class="grid gap-4">
                        @forelse ($upcomingMatches as $match)
                            @php $prediction = $match->predictions->first(); @endphp
                            <form method="POST" action="{{ route('predictions.store', $match) }}" class="wc-card overflow-hidden rounded-lg">
                                @csrf
                                <div class="wc-accent-line"></div>
                                <div class="p-5">
                                    <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-500">{{ $match->tournament->name }}</p>
                                            <p class="text-xs text-gray-500">Cierre: {{ $match->prediction_closes_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <span class="w-fit rounded-full {{ $prediction ? 'bg-blue-50 text-blue-700 ring-blue-200' : 'bg-yellow-50 text-yellow-800 ring-yellow-200' }} px-3 py-1 text-xs font-bold ring-1">
                                            {{ $prediction ? 'Pronostico guardado' : 'Pendiente' }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-3">
                                        <div class="text-center">
                                            @if ($match->homeTeam->logo_path)
                                                <img src="{{ $match->homeTeam->logo_path }}" alt="{{ $match->homeTeam->name }}" class="mx-auto mb-2 h-12 w-16 rounded object-cover ring-1 ring-gray-200">
                                            @endif
                                            <p class="text-sm font-black text-gray-950 sm:text-base">{{ $match->homeTeam->name }}</p>
                                        </div>

                                        <div class="flex items-center gap-2 rounded-lg bg-gray-50 p-2 ring-1 ring-gray-200">
                                            <input name="predicted_home_score" type="number" min="0" max="30" value="{{ old('predicted_home_score', $prediction?->predicted_home_score) }}" class="h-12 w-14 rounded-md border-gray-300 text-center text-xl font-black" required>
                                            <span class="text-lg font-black text-gray-400">-</span>
                                            <input name="predicted_away_score" type="number" min="0" max="30" value="{{ old('predicted_away_score', $prediction?->predicted_away_score) }}" class="h-12 w-14 rounded-md border-gray-300 text-center text-xl font-black" required>
                                        </div>

                                        <div class="text-center">
                                            @if ($match->awayTeam->logo_path)
                                                <img src="{{ $match->awayTeam->logo_path }}" alt="{{ $match->awayTeam->name }}" class="mx-auto mb-2 h-12 w-16 rounded object-cover ring-1 ring-gray-200">
                                            @endif
                                            <p class="text-sm font-black text-gray-950 sm:text-base">{{ $match->awayTeam->name }}</p>
                                        </div>
                                    </div>

                                    <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <p class="text-sm text-gray-500">Se bloquea {{ $match->prediction_closes_at->diffForHumans() }}</p>
                                        <button class="wc-button rounded-md px-5 py-3 text-sm font-black shadow-sm">Guardar pronostico</button>
                                    </div>
                                </div>
                            </form>
                        @empty
                            <div class="wc-card rounded-lg p-8 text-center">
                                <p class="font-semibold text-gray-900">No hay partidos abiertos ahora.</p>
                                <p class="mt-1 text-sm text-gray-500">Cuando tu torneo tenga partidos disponibles, apareceran aqui.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <aside class="space-y-6">
                    <div class="wc-card rounded-lg p-5">
                        <h2 class="text-lg font-black text-gray-950">Mis torneos</h2>
                        <div class="mt-4 space-y-3">
                            @forelse ($participants as $participant)
                                <div class="rounded-lg border border-gray-200 p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="font-bold text-gray-950">{{ $participant->tournament->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $participant->payment_status }}</p>
                                        </div>
                                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $participant->status === 'approved' ? 'bg-green-50 text-green-700 ring-1 ring-green-200' : 'bg-yellow-50 text-yellow-800 ring-1 ring-yellow-200' }}">{{ $participant->status }}</span>
                                    </div>
                                    @if ($participant->status === 'pending_payment' && ($url = $participant->tournament->whatsappPaymentUrl(auth()->user())))
                                        <a class="mt-3 block rounded-md bg-green-600 px-4 py-2 text-center text-sm font-bold text-white hover:bg-green-700" href="{{ $url }}" target="_blank">Pedir medios de pago</a>
                                    @endif
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">Aun no estas inscrito.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="wc-card rounded-lg p-5">
                        <h2 class="text-lg font-black text-gray-950">Torneos disponibles</h2>
                        <div class="mt-4 space-y-3">
                            @foreach ($availableTournaments as $tournament)
                                @unless ($participants->firstWhere('tournament_id', $tournament->id))
                                    <form method="POST" action="{{ route('tournaments.register', $tournament) }}" class="rounded-lg border border-gray-200 p-4">
                                        @csrf
                                        <p class="font-bold text-gray-950">{{ $tournament->name }}</p>
                                        <p class="mb-3 text-sm text-gray-500">{{ $tournament->entry_fee ? $tournament->currency.' '.$tournament->entry_fee : 'Sin costo' }}</p>
                                        <button class="w-full rounded-md border border-blue-200 px-4 py-2 text-sm font-bold text-blue-700 hover:bg-blue-50">Inscribirme</button>
                                    </form>
                                @endunless
                            @endforeach
                        </div>
                    </div>

                    <div class="wc-card rounded-lg p-5">
                        <h2 class="text-lg font-black text-gray-950">Mis posiciones</h2>
                        <div class="mt-4 space-y-3">
                            @forelse ($rankings as $ranking)
                                <a href="{{ route('tournaments.ranking', $ranking->tournament) }}" class="flex items-center justify-between rounded-lg border border-gray-200 p-4 hover:border-blue-300">
                                    <span class="font-bold text-gray-950">{{ $ranking->tournament->name }}</span>
                                    <span class="text-sm font-black text-blue-700">#{{ $ranking->position }} · {{ $ranking->total_points }} pts</span>
                                </a>
                            @empty
                                <p class="text-sm text-gray-500">Aun no tienes ranking calculado.</p>
                            @endforelse
                        </div>
                    </div>
                </aside>
            </section>
        </div>
    </div>
</x-app-layout>
