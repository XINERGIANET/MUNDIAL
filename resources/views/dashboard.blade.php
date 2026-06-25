<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-blue-700">Panel del apostador</p>
                <h1 class="text-2xl font-black text-gray-950">Pronosticos Mundial 2026</h1>
                <p class="mt-1 text-sm text-gray-500">Calendario por grupos, estados y marcadores en una sola pantalla.</p>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:flex">
                <div class="rounded-lg bg-white px-4 py-3 ring-1 ring-gray-200">
                    <p class="text-xs font-bold uppercase text-gray-500">Aprobados</p>
                    <p class="text-xl font-black text-gray-950">{{ $participants->where('status', 'approved')->count() }}</p>
                </div>
                <div class="rounded-lg bg-white px-4 py-3 ring-1 ring-gray-200">
                    <p class="text-xs font-bold uppercase text-gray-500">Abiertos</p>
                    <p class="text-xl font-black text-blue-700">{{ $upcomingMatches->count() }}</p>
                </div>
                <div class="rounded-lg bg-green-50 px-4 py-3 ring-1 ring-green-200">
                    <p class="text-xs font-bold uppercase text-green-700">Celular</p>
                    <p class="text-sm font-black text-green-800">{{ auth()->user()->maskedPhone() }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    @php
        $statusLabels = [
            'scheduled' => 'Programado',
            'live' => 'En vivo',
            'finished' => 'Finalizado',
            'cancelled' => 'Cancelado',
        ];
        $participantLabels = [
            'pending_payment' => 'Pendiente de pago',
            'approved' => 'Aprobado',
            'rejected' => 'Rechazado',
            'suspended' => 'Suspendido',
        ];
        $paymentLabels = [
            'unpaid' => 'No pagado',
            'pending_review' => 'En revision',
            'paid' => 'Pagado',
            'waived' => 'Exonerado',
        ];
        $predictionsFinalized = $selectedParticipant?->hasFinalizedPredictions() ?? false;
        $hasCourtesyOnlyAccess = $selectedParticipant?->hasCourtesyAccess() ?? false;
        $isCourtesyGuest = $selectedTournament && ! $selectedParticipant;
        $canEditPredictions = $selectedTournament && ! $predictionsFinalized && (($selectedParticipant && ($selectedParticipant->isApproved() || $hasCourtesyOnlyAccess)) || $isCourtesyGuest);
    @endphp

    <div class="pb-12">
        <section class="wc-shell">
            <div class="w-full px-4 py-6 sm:px-6 lg:px-8">
                <form method="GET" action="{{ route('dashboard') }}" class="grid gap-3 rounded-xl bg-white/10 p-4 ring-1 ring-white/20 backdrop-blur md:grid-cols-4">
                    <div>
                        <label class="mb-1 block text-xs font-black uppercase tracking-wide text-white/75">Torneo</label>
                        <select name="torneo" class="w-full rounded-lg border-white/20 bg-white text-sm font-semibold text-gray-950">
                            @foreach ($accessibleTournaments as $tournament)
                                <option value="{{ $tournament->id }}" @selected($selectedTournament?->id === $tournament->id)>{{ $tournament->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-black uppercase tracking-wide text-white/75">Grupo</label>
                        <select name="grupo" class="w-full rounded-lg border-white/20 bg-white text-sm font-semibold text-gray-950">
                            <option value="">Todos los grupos</option>
                            @foreach ($tournamentGroups as $group)
                                <option value="{{ $group->id }}" @selected($selectedGroupId === $group->id)>{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-black uppercase tracking-wide text-white/75">Estado</label>
                        <select name="estado" class="w-full rounded-lg border-white/20 bg-white text-sm font-semibold text-gray-950">
                            <option value="abiertos" @selected($selectedStatus === 'abiertos')>Abiertos para pronosticar</option>
                            <option value="cerrados" @selected($selectedStatus === 'cerrados')>Cerrados</option>
                            <option value="resultados" @selected($selectedStatus === 'resultados')>Con resultado</option>
                            <option value="todos" @selected($selectedStatus === 'todos')>Todos</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button class="w-full rounded-lg bg-white px-4 py-2.5 text-sm font-black text-blue-800 hover:bg-blue-50">Filtrar calendario</button>
                    </div>
                </form>
            </div>
        </section>

        <div class="w-full px-4 py-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-lg bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-200">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-lg bg-red-50 p-4 text-sm font-medium text-red-800 ring-1 ring-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (auth()->user()->hasAnyRole(['super_admin', 'tournament_admin']))
                <div class="mb-6 flex flex-col gap-4 rounded-xl border border-blue-200 bg-blue-50 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-wide text-blue-700">Administracion</p>
                        <h2 class="text-xl font-black text-gray-950">Gestiona torneos, partidos, pagos y resultados</h2>
                        <p class="mt-1 text-sm text-gray-600">Tu usuario tiene permisos administrativos. Puedes ir al panel de gestion desde aqui.</p>
                    </div>
                    <a href="{{ url('/admin') }}" class="rounded-lg bg-blue-700 px-5 py-3 text-center text-sm font-black text-white hover:bg-blue-800">
                        Ir al panel administrativo
                    </a>
                </div>
            @endif

            <div class="grid gap-6 2xl:grid-cols-[1fr_420px]">
                <main class="space-y-6">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-black uppercase tracking-wide text-red-600">Calendario</p>
                            <h2 class="text-xl font-black text-gray-950">{{ $selectedTournament?->name ?? 'Sin torneos disponibles' }}</h2>
                            @if ($predictionsFinalized)
                                <p class="mt-1 text-sm font-semibold text-green-700">Pronosticos guardados definitivamente. Ya no se pueden editar.</p>
                            @elseif ($isCourtesyGuest)
                                <p class="mt-1 text-sm font-semibold text-amber-700">Estas viendo partidos de cortesia de bienvenida. Puedes pronosticar sin pagar; al guardar quedaras inscrito como pendiente de pago.</p>
                            @elseif ($hasCourtesyOnlyAccess)
                                <p class="mt-1 text-sm font-semibold text-amber-700">Estas viendo partidos de cortesia de bienvenida. Puedes guardar pronosticos parciales sin haber pagado todavia.</p>
                            @endif
                        </div>
                        <p class="text-sm font-semibold text-gray-500">{{ $tournamentMatches->count() }} partidos encontrados</p>
                    </div>

                    @if ($canEditPredictions)
                        <form method="POST" action="{{ route('predictions.bulk-store', $selectedTournament) }}" class="space-y-6">
                            @csrf
                    @endif

                    @forelse ($tournamentMatches->groupBy(fn ($match) => $match->group?->name ?? $match->phase?->name ?? 'Sin grupo') as $groupName => $matches)
                        <section class="wc-card overflow-hidden rounded-xl">
                            <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50 px-5 py-4">
                                <div>
                                    <h3 class="text-lg font-black text-gray-950">{{ $groupName }}</h3>
                                    <p class="text-sm text-gray-500">{{ $matches->count() }} partidos</p>
                                </div>
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700 ring-1 ring-blue-200">Mundial 2026</span>
                            </div>

                            <div class="divide-y divide-gray-100">
                                @foreach ($matches as $match)
                                    @php
                                        $prediction = $match->predictions->first();
                                        $homeTeam = $match->homeTeam;
                                        $awayTeam = $match->awayTeam;
                                        $hasTeams = $homeTeam && $awayTeam;
                                        $isOpen = $hasTeams && $match->isPredictionOpen() && $homeTeam->is_active && $awayTeam->is_active;
                                        $isCourtesyMatch = (bool) $match->is_welcome_courtesy;
                                        $isEditable = $isOpen && $canEditPredictions && \App\Support\MatchAccess::canParticipantAccess($selectedParticipant, $match);
                                        $isFinished = $match->status === 'finished';
                                    @endphp

                                    <article class="grid gap-4 p-5 xl:grid-cols-[220px_1fr_260px] xl:items-center">
                                        <div>
                                            <p class="text-sm font-black text-gray-950">{{ $match->starts_at->format('d/m/Y') }}</p>
                                            <p class="text-sm text-gray-500">{{ $match->starts_at->format('H:i') }} · cierra {{ $match->prediction_closes_at->format('H:i') }}</p>
                                            <span class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-black ring-1 {{ $isFinished ? 'bg-green-50 text-green-700 ring-green-200' : ($isOpen ? 'bg-blue-50 text-blue-700 ring-blue-200' : 'bg-gray-50 text-gray-600 ring-gray-200') }}">
                                                {{ $isOpen ? 'Abierto' : ($statusLabels[$match->status] ?? $match->status) }}
                                            </span>
                                            @if ($isCourtesyMatch)
                                                <span class="mt-2 inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-black text-amber-700 ring-1 ring-amber-200">Cortesia</span>
                                            @endif
                                        </div>

                                        @if ($isEditable)
                                            <div class="grid grid-cols-[minmax(0,1fr)_3.75rem_2.5rem_3.75rem_minmax(0,1fr)] items-start gap-2 xl:col-span-2 xl:grid-cols-[minmax(220px,1fr)_4.5rem_3rem_4.5rem_minmax(220px,1fr)] xl:items-center xl:gap-4">
                                                <div class="min-w-0 text-center xl:flex xl:items-center xl:gap-3 xl:text-left">
                                                    @if ($homeTeam?->logo_path)
                                                        <img src="{{ $homeTeam->logo_path }}" alt="{{ $homeTeam->name }}" class="mx-auto h-10 w-14 shrink-0 rounded object-cover ring-1 ring-gray-200 xl:mx-0">
                                                    @endif
                                                    <div class="min-w-0">
                                                        <p class="mt-1 truncate text-sm font-black text-gray-950 xl:mt-0 xl:text-base">{{ $homeTeam?->name ?? 'Equipo por definir' }}</p>
                                                        <p class="text-[11px] text-gray-500 xl:text-xs">Local</p>
                                                    </div>
                                                </div>

                                                <input name="predictions[{{ $match->id }}][predicted_home_score]" type="number" min="0" max="30" value="{{ old('predictions.'.$match->id.'.predicted_home_score', $prediction?->predicted_home_score) }}" class="h-12 w-full rounded-lg border-gray-300 text-center text-xl font-black">

                                                <div class="grid h-11 place-items-center rounded-lg bg-gray-100 px-2 text-sm font-black text-gray-500">VS</div>

                                                <input name="predictions[{{ $match->id }}][predicted_away_score]" type="number" min="0" max="30" value="{{ old('predictions.'.$match->id.'.predicted_away_score', $prediction?->predicted_away_score) }}" class="h-12 w-full rounded-lg border-gray-300 text-center text-xl font-black">

                                                <div class="flex min-w-0 flex-col items-center text-center xl:flex-row xl:justify-end xl:gap-3 xl:text-right">
                                                    <div class="min-w-0">
                                                        <p class="mt-1 truncate text-sm font-black text-gray-950 xl:mt-0 xl:text-base">{{ $awayTeam?->name ?? 'Equipo por definir' }}</p>
                                                        <p class="text-[11px] text-gray-500 xl:text-xs">Visitante</p>
                                                    </div>
                                                    @if ($awayTeam?->logo_path)
                                                        <img src="{{ $awayTeam->logo_path }}" alt="{{ $awayTeam->name }}" class="order-first mx-auto h-10 w-14 shrink-0 rounded object-cover ring-1 ring-gray-200 xl:order-none xl:mx-0">
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-3">
                                                <div class="flex items-center gap-3">
                                                    @if ($homeTeam?->logo_path)
                                                        <img src="{{ $homeTeam->logo_path }}" alt="{{ $homeTeam->name }}" class="h-10 w-14 rounded object-cover ring-1 ring-gray-200">
                                                    @endif
                                                    <div>
                                                        <p class="font-black text-gray-950">{{ $homeTeam?->name ?? 'Equipo por definir' }}</p>
                                                        <p class="text-xs text-gray-500">Local</p>
                                                    </div>
                                                </div>

                                                <div class="rounded-lg bg-gray-100 px-3 py-2 text-sm font-black text-gray-500">
                                                    @if ($isFinished)
                                                        {{ $match->home_score }} - {{ $match->away_score }}
                                                    @else
                                                        VS
                                                    @endif
                                                </div>

                                                <div class="flex items-center justify-end gap-3 text-right">
                                                    <div>
                                                        <p class="font-black text-gray-950">{{ $awayTeam?->name ?? 'Equipo por definir' }}</p>
                                                        <p class="text-xs text-gray-500">Visitante</p>
                                                    </div>
                                                    @if ($awayTeam?->logo_path)
                                                        <img src="{{ $awayTeam->logo_path }}" alt="{{ $awayTeam->name }}" class="h-10 w-14 rounded object-cover ring-1 ring-gray-200">
                                                    @endif
                                                </div>
                                            </div>

                                            <div>
                                                @if ($prediction)
                                                <div class="rounded-lg bg-gray-50 p-3 ring-1 ring-gray-200">
                                                    <p class="text-xs font-bold uppercase text-gray-500">Tu pronostico</p>
                                                    <p class="text-lg font-black text-gray-950">{{ $prediction->predicted_home_score }} - {{ $prediction->predicted_away_score }}</p>
                                                    <p class="text-sm text-gray-500">{{ $prediction->points_awarded }} puntos</p>
                                                </div>
                                            @else
                                                <div class="rounded-lg bg-gray-50 p-3 text-sm font-semibold text-gray-500 ring-1 ring-gray-200">Sin pronostico disponible</div>
                                            @endif
                                            </div>
                                        @endif
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @empty
                        <div class="wc-card rounded-xl p-10 text-center">
                            <p class="text-lg font-black text-gray-950">No hay partidos con estos filtros.</p>
                            <p class="mt-1 text-sm text-gray-500">Cambia el grupo o el estado para ver el calendario.</p>
                        </div>
                    @endforelse

                    @if ($canEditPredictions)
                            <div class="wc-card sticky bottom-4 z-10 flex flex-col gap-3 rounded-xl p-4 sm:flex-row sm:items-center sm:justify-end">
                                <button name="save_mode" value="partial" class="rounded-lg border border-blue-200 px-5 py-3 text-sm font-black text-blue-700 hover:bg-blue-50">
                                    Guardar parcial
                                </button>
                                @if ($selectedParticipant?->isApproved())
                                    <button name="save_mode" value="final" class="rounded-lg bg-blue-700 px-5 py-3 text-sm font-black text-white hover:bg-blue-800">
                                        Guardar todo y bloquear
                                    </button>
                                @endif
                            </div>
                        </form>
                    @endif
                </main>

                <aside class="space-y-6">
                    <div class="wc-card rounded-xl p-5">
                        <h2 class="text-lg font-black text-gray-950">Mis torneos</h2>
                        <div class="mt-4 space-y-3">
                            @forelse ($participants as $participant)
                                <div class="rounded-lg border border-gray-200 p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="font-bold text-gray-950">{{ $participant->tournament->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $paymentLabels[$participant->payment_status] ?? $participant->payment_status }}</p>
                                        </div>
                                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $participant->status === 'approved' ? 'bg-green-50 text-green-700 ring-1 ring-green-200' : 'bg-yellow-50 text-yellow-800 ring-1 ring-yellow-200' }}">{{ $participantLabels[$participant->status] ?? $participant->status }}</span>
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

                    <div class="wc-card rounded-xl p-5">
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

                    <div class="wc-card rounded-xl p-5">
                        <h2 class="text-lg font-black text-gray-950">Mi ranking</h2>
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
            </div>
        </div>
    </div>
</x-app-layout>
