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
        $predictionsFinalized  = $selectedParticipant?->hasFinalizedPredictions() ?? false;
        $isApproved            = $selectedParticipant?->isApproved() ?? false;
        $hasPendingParticipant = $selectedTournament
            && $selectedParticipant
            && ! $isApproved;
        $hasNoParticipant      = $selectedTournament && ! $selectedParticipant;
    @endphp

    {{-- Filter bar --}}
    <div class="wc-shell">
        <div class="w-full px-4 py-5 sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('dashboard') }}"
                  class="grid gap-3 rounded-xl bg-white/10 p-4 ring-1 ring-white/20 backdrop-blur md:grid-cols-3">
                <div>
                    <label class="mb-1 block text-xs font-black uppercase tracking-wide text-white/70">Torneo</label>
                    <select name="torneo" class="w-full rounded-lg border-white/20 bg-white text-sm font-semibold text-gray-950">
                        @foreach ($availableTournaments as $tournament)
                            <option value="{{ $tournament->id }}" @selected($selectedTournament?->id === $tournament->id)>{{ $tournament->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-black uppercase tracking-wide text-white/70">Estado</label>
                    <select name="estado" class="w-full rounded-lg border-white/20 bg-white text-sm font-semibold text-gray-950">
                        <option value="abiertos" @selected($selectedStatus === 'abiertos')>Abiertos</option>
                        <option value="cerrados" @selected($selectedStatus === 'cerrados')>Cerrados</option>
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

                {{-- Jugada tabs (solo si hay más de una) --}}
                @if ($tournamentParticipants->count() > 1)
                    <div class="flex gap-2 rounded-xl border border-gray-100 bg-gray-50 p-1.5">
                        @foreach ($tournamentParticipants as $i => $jugada)
                            <a href="{{ route('dashboard', array_filter(['torneo' => $selectedTournament?->id, 'jugada' => $jugada->id, 'estado' => $selectedStatus])) }}"
                               class="flex-1 rounded-lg px-4 py-2.5 text-center text-sm font-black transition
                                   {{ $selectedParticipant?->id === $jugada->id
                                       ? 'bg-white text-gray-950 shadow-sm ring-1 ring-gray-200'
                                       : 'text-gray-500 hover:text-gray-800' }}">
                                {{ $jugada->entry_name ?? ('Jugada ' . ($i + 1)) }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wide text-red-600">Calendario</p>
                        <h2 class="text-xl font-black text-gray-950">{{ $selectedTournament?->name ?? 'Sin torneos disponibles' }}</h2>
                        @if ($predictionsFinalized)
                            <p class="mt-1 text-sm font-semibold text-green-700">Pronósticos guardados definitivamente. Ya no se pueden editar.</p>
                        @elseif ($hasNoParticipant)
                            <p class="mt-1 text-sm font-semibold text-gray-500">Inscríbete en este torneo para poder guardar tus pronósticos.</p>
                        @elseif ($hasPendingParticipant)
                            <p class="mt-1 text-sm font-semibold text-amber-700">Tu inscripción está siendo verificada. Podrás guardar pronósticos una vez aprobado.</p>
                        @endif
                    </div>
                    <p class="text-sm text-gray-400">{{ $tournamentMatches->count() }} partidos</p>
                </div>

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
                                        $prediction      = $match->predictions->first();
                                        $isSaved         = $prediction !== null;
                                        $homeTeam        = $match->homeTeam;
                                        $awayTeam        = $match->awayTeam;
                                        $hasTeams        = $homeTeam && $awayTeam;
                                        $isOpen          = $hasTeams && $match->isPredictionOpen() && $homeTeam->is_active && $awayTeam->is_active;
                                        $canSave = $isOpen && ! $isSaved && $isApproved;
                                        $isFinished      = $match->status === 'finished';
                                        $isLive          = $match->status === 'live';
                                    @endphp

                                    @if ($canSave)
                                        <form method="POST" action="{{ route('predictions.store', $match) }}">
                                            @csrf
                                            <input type="hidden" name="participant_id" value="{{ $selectedParticipant?->id }}">
                                    @endif

                                    <article class="p-5 sm:p-6">

                                        {{-- Top bar: time + badges --}}
                                        <div class="mb-5 flex flex-wrap items-center justify-between gap-2">
                                            <div class="flex items-center gap-2 text-sm">
                                                <span class="font-black text-gray-700">{{ $match->starts_at->format('H:i') }}</span>
                                                <span class="text-gray-300">·</span>
                                                <span class="text-xs text-gray-400">cierra {{ $match->prediction_closes_at->format('H:i') }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                @if ($isLive)
                                                    <span class="rounded-full bg-red-600 px-3 py-1 text-xs font-black text-white animate-pulse">En vivo</span>
                                                @elseif ($isFinished)
                                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-black text-gray-600 ring-1 ring-gray-200">Finalizado</span>
                                                @elseif ($isSaved)
                                                    <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-black text-green-700 ring-1 ring-green-200">Cerrado</span>
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

                                            {{-- Center: inputs / saved / result / VS --}}
                                            <div class="flex flex-shrink-0 items-center gap-2">
                                                @if ($isSaved)
                                                    <div class="rounded-xl bg-green-50 px-5 py-3 text-center ring-1 ring-green-200">
                                                        <p class="text-xl font-black text-green-800 tracking-widest">{{ $prediction->predicted_home_score }} – {{ $prediction->predicted_away_score }}</p>
                                                        <p class="mt-0.5 text-[10px] font-bold uppercase text-green-600">Mi pronóstico</p>
                                                    </div>
                                                @elseif ($canSave)
                                                    <input name="predicted_home_score"
                                                           type="number" min="0" max="30"
                                                           value="{{ old('predicted_home_score') }}"
                                                           placeholder="0"
                                                           class="h-14 w-14 rounded-xl border-gray-200 bg-white text-center text-2xl font-black text-gray-950 shadow-sm focus:border-blue-400 focus:ring-blue-200">
                                                    <span class="text-xl font-black text-gray-300">:</span>
                                                    <input name="predicted_away_score"
                                                           type="number" min="0" max="30"
                                                           value="{{ old('predicted_away_score') }}"
                                                           placeholder="0"
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

                                        {{-- Save button / points row --}}
                                        <div class="mt-5 border-t border-gray-100 pt-4">
                                            @if ($canSave)
                                                <div class="flex justify-end">
                                                    <button type="submit"
                                                            class="rounded-xl bg-blue-700 px-6 py-2.5 text-sm font-black text-white hover:bg-blue-800">
                                                        Guardar pronóstico
                                                    </button>
                                                </div>
                                            @elseif ($isSaved && $prediction->points_awarded > 0)
                                                <div class="flex items-center justify-between">
                                                    <p class="text-xs font-bold uppercase tracking-wide text-gray-400">Puntos</p>
                                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-sm font-black text-blue-700 ring-1 ring-blue-200">
                                                        {{ $prediction->points_awarded }} pts
                                                    </span>
                                                </div>
                                            @elseif (! $isSaved && ! $isOpen)
                                                <p class="text-xs font-semibold text-gray-400">Sin pronóstico registrado</p>
                                            @endif
                                        </div>

                                    </article>

                                    @if ($canSave)
                                        </form>
                                    @endif
                                @endforeach
                            </div>{{-- /day card --}}

                        @endforeach

                    </div>{{-- /phase --}}

                @empty
                    <div class="wc-card rounded-2xl p-12 text-center">
                        <p class="text-lg font-black text-gray-950">No hay partidos con estos filtros.</p>
                        <p class="mt-2 text-sm text-gray-400">Cambia el estado o el torneo para ver el calendario.</p>
                    </div>
                @endforelse

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
                                        <p class="font-black text-gray-950">{{ $participant->displayName() }}</p>
                                        <p class="text-xs text-gray-400">{{ $participant->tournament->name }}</p>
                                        <p class="mt-0.5 text-sm text-gray-400">{{ $paymentLabels[$participant->payment_status] ?? $participant->payment_status }}</p>
                                    </div>
                                    <span class="rounded-full px-3 py-1 text-xs font-bold
                                        {{ $participant->status === 'approved' ? 'bg-green-50 text-green-700 ring-1 ring-green-200' : 'bg-amber-50 text-amber-700 ring-1 ring-amber-200' }}">
                                        {{ $participantLabels[$participant->status] ?? $participant->status }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400">Aún no estás inscrito en ningún torneo.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Available tournaments + add jugada --}}
                @if ($availableTournaments->isNotEmpty())
                    <div class="wc-card rounded-2xl p-5">
                        <h2 class="text-sm font-black uppercase tracking-wide text-gray-950">Torneos disponibles</h2>
                        <div class="mt-4 space-y-3">
                            @foreach ($availableTournaments as $availTournament)
                                @php $hasJugada = $participants->firstWhere('tournament_id', $availTournament->id); @endphp
                                <div x-data="{ open: false }">
                                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                                        <p class="font-black text-gray-950">{{ $availTournament->name }}</p>
                                        <p class="mb-3 text-sm text-gray-400">{{ $availTournament->entry_fee ? $availTournament->currency.' '.$availTournament->entry_fee : 'Sin costo' }}</p>
                                        <button type="button" @click="open = true"
                                                class="w-full rounded-lg border border-blue-200 px-4 py-2 text-sm font-black text-blue-700 hover:bg-blue-50">
                                            {{ $hasJugada ? 'Agregar otra jugada' : 'Inscribirme' }}
                                        </button>
                                    </div>

                                    {{-- Payment modal --}}
                                    <div x-show="open" style="display:none"
                                         class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                        {{-- Backdrop --}}
                                        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="open = false"></div>

                                        {{-- Dialog --}}
                                        <div x-show="open"
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0 scale-95"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-150"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-95"
                                             class="relative z-10 w-full max-w-sm rounded-2xl bg-white shadow-2xl">

                                            {{-- Header --}}
                                            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                                                <div>
                                                    <p class="text-[10px] font-black uppercase tracking-widest text-blue-600">Inscripción</p>
                                                    <h3 class="text-base font-black text-gray-950">{{ $availTournament->name }}</h3>
                                                </div>
                                                <button type="button" @click="open = false"
                                                        class="rounded-full p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>

                                            {{-- Body --}}
                                            <form method="POST" action="{{ route('tournaments.register', $availTournament) }}"
                                                  enctype="multipart/form-data">
                                                @csrf
                                                <div class="space-y-4 px-6 py-5">

                                                    {{-- QR placeholder / image --}}
                                                    <div class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-200 py-6">
                                                        @if ($availTournament->payment_qr_path)
                                                            <img src="{{ Storage::url($availTournament->payment_qr_path) }}"
                                                                 alt="QR Yape"
                                                                 class="mx-auto h-44 w-44 object-contain">
                                                        @else
                                                            <div class="mb-2 flex h-24 w-24 items-center justify-center rounded-xl bg-purple-50 text-4xl font-black text-purple-300">
                                                                QR
                                                            </div>
                                                            <p class="text-xs font-semibold text-gray-400">Imagen del QR de Yape</p>
                                                            <p class="text-[10px] text-gray-300">(pendiente de configurar)</p>
                                                        @endif
                                                    </div>

                                                    {{-- Yape number --}}
                                                    @if ($availTournament->payment_yape_number)
                                                        <div class="rounded-xl bg-purple-50 px-4 py-3 text-center ring-1 ring-purple-200">
                                                            <p class="text-[10px] font-black uppercase tracking-widest text-purple-500">Número Yape</p>
                                                            <p class="mt-1 text-2xl font-black tracking-widest text-purple-900">{{ $availTournament->payment_yape_number }}</p>
                                                        </div>
                                                    @endif

                                                    {{-- Amount --}}
                                                    @if ($availTournament->entry_fee)
                                                        <p class="text-center text-sm text-gray-500">
                                                            Monto a pagar: <span class="font-black text-gray-950">{{ $availTournament->currency }} {{ number_format($availTournament->entry_fee, 2) }}</span>
                                                        </p>
                                                    @endif

                                                    {{-- Entry name --}}
                                                    <div>
                                                        <label class="mb-1.5 block text-xs font-black uppercase tracking-wide text-gray-600">
                                                            Nombre de tu jugada <span class="font-normal normal-case text-gray-400">(opcional)</span>
                                                        </label>
                                                        <input type="text" name="entry_name" maxlength="60"
                                                               placeholder="Ej: Los Cracks del 26"
                                                               class="w-full rounded-lg border-gray-200 text-sm text-gray-950 placeholder:text-gray-300 focus:border-blue-400 focus:ring-blue-200">
                                                    </div>

                                                    {{-- Payment proof upload --}}
                                                    <div>
                                                        <label class="mb-1.5 block text-xs font-black uppercase tracking-wide text-gray-600">
                                                            Captura del pago <span class="text-red-500">*</span>
                                                        </label>
                                                        <input type="file" name="payment_proof" accept="image/*,.pdf" required
                                                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-600 file:mr-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-xs file:font-black file:text-blue-700 hover:file:bg-blue-100">
                                                        <p class="mt-1 text-[10px] text-gray-400">Foto o captura de pantalla del pago por Yape.</p>
                                                    </div>

                                                </div>

                                                {{-- Footer --}}
                                                <div class="rounded-b-2xl border-t border-gray-100 bg-gray-50 px-6 py-4">
                                                    <button type="submit"
                                                            class="w-full rounded-xl bg-blue-700 px-6 py-3 text-sm font-black text-white hover:bg-blue-800 active:bg-blue-900">
                                                        Confirmar inscripción
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
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
