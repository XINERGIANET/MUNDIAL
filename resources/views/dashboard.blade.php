<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mi dashboard</h2>
            <span class="text-sm text-gray-500">{{ auth()->user()->maskedPhone() }}</span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-green-700 text-sm">{{ session('status') }}</div>
            @endif

            <section class="grid gap-4 md:grid-cols-3">
                <div class="bg-white border border-gray-200 rounded-lg p-5">
                    <p class="text-sm text-gray-500">Celular</p>
                    <p class="mt-1 font-semibold text-gray-900">Verificado</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-5">
                    <p class="text-sm text-gray-500">Torneos aprobados</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $participants->where('status', 'approved')->count() }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-5">
                    <p class="text-sm text-gray-500">Partidos por pronosticar</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $upcomingMatches->count() }}</p>
                </div>
            </section>

            <section>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Torneos disponibles</h3>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($availableTournaments as $tournament)
                        @php $participant = $participants->firstWhere('tournament_id', $tournament->id); @endphp
                        <div class="bg-white border border-gray-200 rounded-lg p-5 space-y-3">
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $tournament->name }}</h4>
                                <p class="text-sm text-gray-500">{{ $tournament->entry_fee ? $tournament->currency.' '.$tournament->entry_fee : 'Sin costo configurado' }}</p>
                            </div>
                            @if (! $participant)
                                <form method="POST" action="{{ route('tournaments.register', $tournament) }}">
                                    @csrf
                                    <x-primary-button class="w-full justify-center">Inscribirme</x-primary-button>
                                </form>
                            @elseif ($participant->status === 'pending_payment')
                                <span class="inline-flex rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-800">Pendiente de pago</span>
                                @if ($url = $tournament->whatsappPaymentUrl(auth()->user()))
                                    <a class="block rounded-md bg-green-600 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-green-700" href="{{ $url }}" target="_blank">Solicitar medios de pago por WhatsApp</a>
                                @endif
                            @else
                                <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800">{{ $participant->status }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>

            <section>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Proximos partidos</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    @forelse ($upcomingMatches as $match)
                        @php $prediction = $match->predictions->first(); @endphp
                        <form method="POST" action="{{ route('predictions.store', $match) }}" class="bg-white border border-gray-200 rounded-lg p-5">
                            @csrf
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-center flex-1">
                                    <p class="font-semibold text-gray-900">{{ $match->homeTeam->name }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input name="predicted_home_score" type="number" min="0" max="30" value="{{ old('predicted_home_score', $prediction?->predicted_home_score) }}" class="w-16 rounded-md border-gray-300 text-center" required>
                                    <span class="text-gray-400">-</span>
                                    <input name="predicted_away_score" type="number" min="0" max="30" value="{{ old('predicted_away_score', $prediction?->predicted_away_score) }}" class="w-16 rounded-md border-gray-300 text-center" required>
                                </div>
                                <div class="text-center flex-1">
                                    <p class="font-semibold text-gray-900">{{ $match->awayTeam->name }}</p>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center justify-between text-sm text-gray-500">
                                <span>Cierra {{ $match->prediction_closes_at->diffForHumans() }}</span>
                                <span>{{ $prediction ? 'Guardado' : 'Pendiente' }}</span>
                            </div>
                            <x-primary-button class="mt-4 w-full justify-center">Guardar pronostico</x-primary-button>
                        </form>
                    @empty
                        <p class="text-sm text-gray-500">No hay partidos abiertos para pronosticar.</p>
                    @endforelse
                </div>
            </section>

            <section>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Mis posiciones</h3>
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    @forelse ($rankings as $ranking)
                        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3 last:border-0">
                            <span class="font-medium">{{ $ranking->tournament->name }}</span>
                            <span class="text-sm text-gray-600">#{{ $ranking->position }} · {{ $ranking->total_points }} pts</span>
                        </div>
                    @empty
                        <p class="p-4 text-sm text-gray-500">Aun no tienes puntos calculados.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
