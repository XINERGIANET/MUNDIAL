<x-app-layout>
    @php
        $statusLabels = [
            'scheduled' => 'Programado',
            'live'      => 'En vivo',
            'finished'  => 'Finalizado',
            'cancelled' => 'Cancelado',
        ];
        $participantLabels = [
            'pending_payment' => 'Pendiente de pago',
            'approved'        => 'Aprobado',
            'rejected'        => 'Rechazado',
            'suspended'       => 'Suspendido',
        ];
        $paymentLabels = [
            'unpaid'         => 'No pagado',
            'pending_review' => 'En revisión',
            'paid'           => 'Pagado',
            'waived'         => 'Exonerado',
        ];
        $predictionsFinalized = $selectedParticipant?->hasFinalizedPredictions() ?? false;
        $hasCourtesyOnlyAccess = $selectedParticipant?->hasCourtesyAccess() ?? false;
        $isCourtesyGuest = $selectedTournament && ! $selectedParticipant;
        $canEditPredictions = $selectedTournament && ! $predictionsFinalized && (($selectedParticipant && ($selectedParticipant->isApproved() || $hasCourtesyOnlyAccess)) || $isCourtesyGuest);
    @endphp

    {{-- Filter bar --}}
    <div class="wc-shell">
        <div class="w-full px-4 py-5 sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('dashboard') }}"
                  class="grid gap-3 rounded-xl bg-white/10 p-4 ring-1 ring-white/20 backdrop-blur md:grid-cols-4">
                <div>
                    <label class="mb-1 block text-xs font-black uppercase tracking-wide text-white/70">Torneo</label>
                    <select name="torneo" class="w-full rounded-lg border-white/20 bg-white text-sm font-semibold text-gray-950">
                        @foreach ($accessibleTournaments as $tournament)
                            <option value="{{ $tournament->id }}" @selected($selectedTournament?->id === $tournament->id)>{{ $tournament->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-black uppercase tracking-wide text-white/70">Grupo</label>
                    <select name="grupo" class="w-full rounded-lg border-white/20 bg-white text-sm font-semibold text-gray-950">
                        <option value="">Todos los grupos</option>
                        @foreach ($tournamentGroups as $group)
                            <option value="{{ $group->id }}" @selected($selectedGroupId === $group->id)>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-black uppercase tracking-wide text-white/70">Estado</label>
                    <select name="estado" class="w-full rounded-lg border-white/20 bg-white text-sm font-semibold text-gray-950">
                        <option value="abiertos"   @selected($selectedStatus === 'abiertos')>Abiertos para pronosticar</option>
                        <option value="cerrados"   @selected($selectedStatus === 'cerrados')>Cerrados</option>
                        <option value="resultados" @selected($selectedStatus === 'resultados')>Con resultado</option>
                        <option value="todos"      @selected($selectedStatus === 'todos')>Todos</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button class="w-full rounded-lg bg-white px-4 py-2.5 text-sm font-black text-blue-800 hover:bg-blue-50">
                        Filtrar calendario
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="w-full px-4 py-8 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="mb-6 rounded-lg bg-green-50 p-4 text-sm font-semibold text-green-800 ring-1 ring-green-200">
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-6 rounded-lg bg-red-50 p-4 text-sm font-semibold text-red-800 ring-1 ring-red-200">
                {{ $errors->first() }}
            </div>
        @endif

        @if (auth()->user()->hasAnyRole(['super_admin', 'tournament_admin']))
            <div class="mb-6 flex flex-col gap-4 rounded-xl border border-blue-200 bg-blue-50 p-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-blue-700">Administración</p>
                    <h2 class="text-lg font-black text-gray-950">Gestiona torneos, partidos y resultados</h2>
                </div>
                <a href="{{ url('/admin') }}" class="rounded-lg bg-blue-700 px-5 py-3 text-center text-sm font-black text-white hover:bg-blue-800">
                    Ir al panel administrativo
                </a>
            </div>
        @endif

        <div class="grid gap-8 xl:grid-cols-[1fr_360px]">

            {{-- ── Main calendar ── --}}
            <main class="min-w-0 space-y-8">
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wide text-red-600">Calendario</p>
                        <h2 class="text-xl font-black text-gray-950">{{ $selectedTournament?->name ?? 'Sin torneos disponibles' }}</h2>
                        @if ($predictionsFinalized)
                            <p class="mt-1 text-sm font-semibold text-green-700">Pronósticos guardados definitivamente. Ya no se pueden editar.</p>
                        @elseif ($isCourtesyGuest)
                            <p class="mt-1 text-sm font-semibold text-amber-700">Estás viendo partidos de cortesía. Puedes pronosticar; al guardar quedarás inscrito como pendiente de pago.</p>
                        @elseif ($hasCourtesyOnlyAccess)
                            <p class="mt-1 text-sm font-semibold text-amber-700">Acceso de cortesía. Puedes guardar pronósticos parciales sin haber pagado.</p>
                        @endif
                    </div>
                    <p class="text-sm text-gray-400">{{ $tournamentMatches->count() }} partidos</p>
                </div>

                @if ($canEditPredictions)
                    <form method="POST" action="{{ route('predictions.bulk-store', $selectedTournament) }}" class="space-y-8">
                        @csrf
                @endif

                @forelse ($tournamentMatches->groupBy(fn ($m) => $m->group?->name ?? $m->phase?->name ?? 'Sin fase') as $phaseName => $phaseMatches)
                    <div class="space-y-3">

                        {{-- Phase header --}}
                        <div class="flex items-center gap-3">
                            <div class="h-px flex-1 bg-gray-200"></div>
                            <span class="rounded-full bg-blue-50 px-4 py-1 text-xs font-black uppercase tracking-wide text-blue-700 ring-1 ring-blue-200">
                                {{ $phaseName }} · {{ $phaseMatches->count() }} partidos
                            </span>
                            <div class="h-px flex-1 bg-gray-200"></div>
                        </div>

                        @foreach ($phaseMatches->groupBy(fn ($m) => $m->starts_at->format('Y-m-d')) as $date => $dateMatches)

                            {{-- Day separator --}}
                            <p class="mt-4 text-xs font-black uppercase tracking-widest text-gray-400">
                                {{ $dateMatches->first()->starts_at->locale('es')->isoFormat('dddd D [de] MMMM') }}
                            </p>

                            <div class="wc-card overflow-hidden rounded-2xl divide-y divide-gray-100">
                                @foreach ($dateMatches as $match)
                                    @php
                                        $prediction  = $match->predictions->first();
                                        $homeTeam    = $match->homeTeam;
                                        $awayTeam    = $match->awayTeam;
                                        $hasTeams    = $homeTeam && $awayTeam;
                                        $isOpen      = $hasTeams && $match->isPredictionOpen() && $homeTeam->is_active && $awayTeam->is_active;
                                        $isCourtesyMatch = (bool) $match->is_welcome_courtesy;
                                        $isEditable  = $isOpen && $canEditPredictions && \App\Support\MatchAccess::canParticipantAccess($selectedParticipant, $match);
                                        $isFinished  = $match->status === 'finished';
                                        $isLive      = $match->status === 'live';
                                    @endphp

                                    <article class="p-5 sm:p-6">

                                        {{-- Top bar: time + badges --}}
                                        <div class="mb-5 flex flex-wrap items-center justify-between gap-2">
                                            <div class="flex items-center gap-2 text-sm">
                                                <span class="font-black text-gray-700">{{ $match->starts_at->format('H:i') }}</span>
                                                <span class="text-gray-300">·</span>
                                                <span class="text-xs text-gray-400">cierra {{ $match->prediction_closes_at->format('H:i') }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                @if ($isCourtesyMatch)
                                                    <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-black text-amber-700 ring-1 ring-amber-200">Cortesía</span>
                                                @endif
                                                @if ($isLive)
                                                    <span class="rounded-full bg-red-600 px-3 py-1 text-xs font-black text-white animate-pulse">En vivo</span>
                                                @elseif ($isFinished)
                                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-black text-gray-600 ring-1 ring-gray-200">Finalizado</span>
                                                @elseif ($isOpen)
                                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700 ring-1 ring-blue-200">Abierto</span>
                                                @else
                                                    <span class="rounded-full bg-gray-50 px-3 py-1 text-xs font-black text-gray-500 ring-1 ring-gray-200">{{ $statusLabels[$match->status] ?? $match->status }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Teams + score --}}
                                        <div class="flex items-center gap-3 sm:gap-6">

                                            {{-- Home team --}}
                                            <div class="flex flex-1 flex-col items-center gap-2 text-center">
                                                @if ($homeTeam?->logo_path)
                                                    <img src="{{ $homeTeam->logo_path }}" alt="{{ $homeTeam->name }}"
                                                         class="h-14 w-20 rounded-lg object-contain ring-1 ring-gray-100">
                                                @else
                                                    <div class="flex h-14 w-20 items-center justify-center rounded-lg bg-gray-100 text-2xl text-gray-300 ring-1 ring-gray-200">?</div>
                                                @endif
                                                <div>
                                                    <p class="font-black text-gray-950 leading-tight">{{ $homeTeam?->name ?? 'Por definir' }}</p>
                                                    <p class="text-xs font-bold uppercase tracking-wide text-gray-400">Local</p>
                                                </div>
                                            </div>

                                            {{-- Center: inputs / result / VS --}}
                                            <div class="flex flex-shrink-0 items-center gap-2">
                                                @if ($isEditable)
                                                    <input name="predictions[{{ $match->id }}][predicted_home_score]"
                                                           type="number" min="0" max="30"
                                                           value="{{ old('predictions.'.$match->id.'.predicted_home_score', $prediction?->predicted_home_score) }}"
                                                           class="h-14 w-14 rounded-xl border-gray-200 bg-white text-center text-2xl font-black text-gray-950 shadow-sm focus:border-blue-400 focus:ring-blue-200">
                                                    <span class="text-xl font-black text-gray-300">:</span>
                                                    <input name="predictions[{{ $match->id }}][predicted_away_score]"
                                                           type="number" min="0" max="30"
                                                           value="{{ old('predictions.'.$match->id.'.predicted_away_score', $prediction?->predicted_away_score) }}"
                                                           class="h-14 w-14 rounded-xl border-gray-200 bg-white text-center text-2xl font-black text-gray-950 shadow-sm focus:border-blue-400 focus:ring-blue-200">
                                                @elseif ($isFinished || $isLive)
                                                    <div class="rounded-xl bg-gray-950 px-5 py-3 text-center">
                                                        <p class="text-xl font-black text-white tracking-widest">{{ $match->home_score }} – {{ $match->away_score }}</p>
                                                        @if ($isLive)<p class="text-[10px] font-bold uppercase text-red-400 mt-0.5">En vivo</p>@endif
                                                    </div>
                                                @else
                                                    <div class="rounded-xl border-2 border-dashed border-gray-200 px-5 py-3">
                                                        <p class="text-sm font-black text-gray-300">VS</p>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Away team --}}
                                            <div class="flex flex-1 flex-col items-center gap-2 text-center">
                                                @if ($awayTeam?->logo_path)
                                                    <img src="{{ $awayTeam->logo_path }}" alt="{{ $awayTeam->name }}"
                                                         class="h-14 w-20 rounded-lg object-contain ring-1 ring-gray-100">
                                                @else
                                                    <div class="flex h-14 w-20 items-center justify-center rounded-lg bg-gray-100 text-2xl text-gray-300 ring-1 ring-gray-200">?</div>
                                                @endif
                                                <div>
                                                    <p class="font-black text-gray-950 leading-tight">{{ $awayTeam?->name ?? 'Por definir' }}</p>
                                                    <p class="text-xs font-bold uppercase tracking-wide text-gray-400">Visitante</p>
                                                </div>
                                            </div>

                                        </div>{{-- /teams --}}

                                        {{-- Prediction row (only when not editable) --}}
                                        @if (! $isEditable)
                                            <div class="mt-5 border-t border-gray-100 pt-4">
                                                @if ($prediction)
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center gap-3">
                                                            <p class="text-xs font-bold uppercase tracking-wide text-gray-400">Mi pronóstico</p>
                                                            <p class="text-base font-black text-gray-950">{{ $prediction->predicted_home_score }} – {{ $prediction->predicted_away_score }}</p>
                                                        </div>
                                                        @if ($prediction->points_awarded !== null)
                                                            <span class="rounded-full bg-blue-50 px-3 py-1 text-sm font-black text-blue-700 ring-1 ring-blue-200">
                                                                {{ $prediction->points_awarded }} pts
                                                            </span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <p class="text-xs font-semibold text-gray-400">Sin pronóstico registrado</p>
                                                @endif
                                            </div>
                                        @endif

                                    </article>
                                @endforeach
                            </div>{{-- /day card --}}

                        @endforeach

                    </div>{{-- /phase --}}

                @empty
                    <div class="wc-card rounded-2xl p-12 text-center">
                        <p class="text-lg font-black text-gray-950">No hay partidos con estos filtros.</p>
                        <p class="mt-2 text-sm text-gray-400">Cambia el grupo o el estado para ver el calendario.</p>
                    </div>
                @endforelse

                @if ($canEditPredictions)
                        <div class="wc-card sticky bottom-4 z-10 flex flex-col gap-3 rounded-2xl p-4 sm:flex-row sm:items-center sm:justify-end">
                            <button name="save_mode" value="partial"
                                    class="rounded-xl border border-blue-200 px-6 py-3 text-sm font-black text-blue-700 hover:bg-blue-50">
                                Guardar borrador
                            </button>
                            @if ($selectedParticipant?->isApproved())
                                <button name="save_mode" value="final"
                                        class="rounded-xl bg-blue-700 px-6 py-3 text-sm font-black text-white hover:bg-blue-800">
                                    Guardar y bloquear pronósticos
                                </button>
                            @endif
                        </div>
                    </form>
                @endif
            </main>

            {{-- ── Sidebar ── --}}
            <aside class="space-y-5">

                {{-- Stats --}}
                <div class="wc-card rounded-2xl p-5">
                    <h2 class="text-xs font-black uppercase tracking-wide text-gray-400 mb-4">Mi resumen</h2>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="flex flex-col items-center rounded-xl bg-gray-50 p-3 ring-1 ring-gray-100">
                            <span class="text-2xl font-black text-gray-950">{{ $participants->where('status', 'approved')->count() }}</span>
                            <span class="mt-1 text-[10px] font-bold uppercase tracking-wide text-gray-400">Aprobados</span>
                        </div>
                        <div class="flex flex-col items-center rounded-xl bg-blue-50 p-3 ring-1 ring-blue-100">
                            <span class="text-2xl font-black text-blue-700">{{ $upcomingMatches->count() }}</span>
                            <span class="mt-1 text-[10px] font-bold uppercase tracking-wide text-blue-500">Abiertos</span>
                        </div>
                        <div class="flex flex-col items-center rounded-xl bg-green-50 p-3 ring-1 ring-green-100">
                            <span class="text-sm font-black text-green-800">{{ auth()->user()->maskedPhone() }}</span>
                            <span class="mt-1 text-[10px] font-bold uppercase tracking-wide text-green-600">Celular</span>
                        </div>
                    </div>
                </div>

                {{-- My tournaments --}}
                <div class="wc-card rounded-2xl p-5">
                    <h2 class="text-sm font-black uppercase tracking-wide text-gray-950">Mis torneos</h2>
                    <div class="mt-4 space-y-3">
                        @forelse ($participants as $participant)
                            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-black text-gray-950">{{ $participant->tournament->name }}</p>
                                        <p class="text-sm text-gray-400">{{ $paymentLabels[$participant->payment_status] ?? $participant->payment_status }}</p>
                                    </div>
                                    <span class="rounded-full px-3 py-1 text-xs font-bold
                                        {{ $participant->status === 'approved' ? 'bg-green-50 text-green-700 ring-1 ring-green-200' : 'bg-amber-50 text-amber-700 ring-1 ring-amber-200' }}">
                                        {{ $participantLabels[$participant->status] ?? $participant->status }}
                                    </span>
                                </div>
                                @if ($participant->status === 'pending_payment' && ($url = $participant->tournament->whatsappPaymentUrl(auth()->user())))
                                    <a href="{{ $url }}" target="_blank"
                                       class="mt-3 block rounded-lg bg-green-600 px-4 py-2.5 text-center text-sm font-black text-white hover:bg-green-700">
                                        Pedir medios de pago
                                    </a>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-400">Aún no estás inscrito en ningún torneo.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Available tournaments --}}
                @php $availableToJoin = $availableTournaments->filter(fn ($t) => ! $participants->firstWhere('tournament_id', $t->id)); @endphp
                @if ($availableToJoin->isNotEmpty())
                    <div class="wc-card rounded-2xl p-5">
                        <h2 class="text-sm font-black uppercase tracking-wide text-gray-950">Torneos disponibles</h2>
                        <div class="mt-4 space-y-3">
                            @foreach ($availableToJoin as $tournament)
                                <form method="POST" action="{{ route('tournaments.register', $tournament) }}"
                                      class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                                    @csrf
                                    <p class="font-black text-gray-950">{{ $tournament->name }}</p>
                                    <p class="mb-3 text-sm text-gray-400">{{ $tournament->entry_fee ? $tournament->currency.' '.$tournament->entry_fee : 'Sin costo' }}</p>
                                    <button class="w-full rounded-lg border border-blue-200 px-4 py-2 text-sm font-black text-blue-700 hover:bg-blue-50">
                                        Inscribirme
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Ranking --}}
                <div class="wc-card rounded-2xl p-5">
                    <h2 class="text-sm font-black uppercase tracking-wide text-gray-950">Mi ranking</h2>
                    <div class="mt-4 space-y-3">
                        @forelse ($rankings as $ranking)
                            <a href="{{ route('tournaments.ranking', $ranking->tournament) }}"
                               class="flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 p-4 hover:border-blue-200 transition">
                                <span class="font-black text-gray-950">{{ $ranking->tournament->name }}</span>
                                <span class="text-sm font-black text-blue-700">#{{ $ranking->position }} · {{ $ranking->total_points }} pts</span>
                            </a>
                        @empty
                            <p class="text-sm text-gray-400">Aún no tienes ranking calculado.</p>
                        @endforelse
                    </div>
                </div>

            </aside>
        </div>
    </div>
</x-app-layout>
