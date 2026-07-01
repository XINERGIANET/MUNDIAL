<x-app-layout>
    @php
        $resultColors = [
            'exact_score'    => 'bg-green-50 text-green-800 ring-1 ring-green-200',
            'correct_result' => 'bg-blue-50 text-blue-800 ring-1 ring-blue-200',
            'wrong'          => 'bg-red-50 text-red-700 ring-1 ring-red-200',
            'pending'        => 'bg-gray-50 text-gray-500 ring-1 ring-gray-200',
        ];
        $positionStyles = [
            1 => 'bg-yellow-400 text-yellow-900',
            2 => 'bg-gray-300 text-gray-800',
            3 => 'bg-amber-600 text-white',
        ];
    @endphp

    <div class="min-h-screen bg-[#f6f8fb]">
        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

            {{-- Page title --}}
            <div class="mb-8">
                <p class="text-xs font-black uppercase tracking-widest text-red-600">Tabla general</p>
                <h1 class="text-3xl font-black text-gray-950">{{ $tournament->name }}</h1>
                @if ($tournament->description)
                    <p class="mt-1 text-sm text-gray-500">{{ $tournament->description }}</p>
                @endif
            </div>

            {{-- ── Ranking table ── --}}
            <section class="mb-10">
                <h2 class="mb-3 text-xs font-black uppercase tracking-widest text-gray-400">Posiciones</h2>
                <div class="wc-card overflow-hidden rounded-2xl">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-black uppercase tracking-wide text-gray-500">
                                <th class="px-5 py-3">#</th>
                                <th class="px-5 py-3">Jugada</th>
                                <th class="hidden px-5 py-3 sm:table-cell">Participante</th>
                                <th class="px-5 py-3 text-right">Puntos</th>
                                <th class="hidden px-5 py-3 text-right sm:table-cell">Exactos</th>
                                <th class="hidden px-5 py-3 text-right md:table-cell">Correctos</th>
                                <th class="hidden px-5 py-3 text-right md:table-cell">Fallos</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($rankings as $ranking)
                                <tr class="hover:bg-gray-50/60 transition-colors">
                                    <td class="px-5 py-4">
                                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full text-xs font-black
                                            {{ $positionStyles[$ranking->position] ?? 'bg-gray-100 text-gray-600' }}">
                                            {{ $ranking->position }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <p class="font-black text-gray-950">{{ $ranking->participant?->displayName() ?? $ranking->user?->name }}</p>
                                    </td>
                                    <td class="hidden px-5 py-4 text-gray-500 sm:table-cell">
                                        {{ $ranking->user?->name }}
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <span class="text-lg font-black text-gray-950">{{ $ranking->total_points }}</span>
                                        <span class="text-xs text-gray-400"> pts</span>
                                    </td>
                                    <td class="hidden px-5 py-4 text-right sm:table-cell">
                                        <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-black text-green-700 ring-1 ring-green-200">
                                            {{ $ranking->exact_scores_count }} ✓✓
                                        </span>
                                    </td>
                                    <td class="hidden px-5 py-4 text-right md:table-cell">
                                        <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-black text-blue-700 ring-1 ring-blue-200">
                                            {{ $ranking->correct_results_count }} ✓
                                        </span>
                                    </td>
                                    <td class="hidden px-5 py-4 text-right md:table-cell">
                                        <span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-black text-red-600 ring-1 ring-red-100">
                                            {{ $ranking->wrong_predictions_count }} ✗
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-400">
                                        Aún no hay ranking calculado. Los puntos se actualizan tras cada partido.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Legend --}}
                <div class="mt-3 flex flex-wrap gap-4 text-xs text-gray-500">
                    <span><span class="font-black text-green-700">✓✓ Exacto</span> — marcador correcto (máx. puntos)</span>
                    <span><span class="font-black text-blue-700">✓ Correcto</span> — resultado correcto, marcador diferente</span>
                    <span><span class="font-black text-red-600">✗ Fallo</span> — resultado incorrecto</span>
                </div>
            </section>

            {{-- ── Predictions grid ── --}}
            @if ($matches->isNotEmpty() && $rankings->isNotEmpty())
                <section>
                    <h2 class="mb-3 text-xs font-black uppercase tracking-widest text-gray-400">Pronósticos por partido</h2>
                    <p class="mb-4 text-xs text-gray-400">Los pronósticos de cada partido se revelan 10 minutos antes de su inicio, cuando la ventana de predicción cierra. Si un usuario ya tiene registrados varios partidos, sus resultados se irán mostrando de forma progresiva, uno a uno, conforme vaya cerrando la ventana de cada encuentro.</p>

                    <div class="wc-card overflow-x-auto rounded-2xl">
                        <table class="min-w-full text-xs">
                            <thead>
                                {{-- Phase row --}}
                                <tr class="border-b border-gray-100 bg-gray-50">
                                    <th class="sticky left-0 z-10 min-w-[160px] bg-gray-50 px-4 py-2 text-left text-[10px] font-black uppercase tracking-wide text-gray-400">
                                        Jugada
                                    </th>
                                    @foreach ($matches as $match)
                                        @php $isClosed = $match->prediction_closes_at->isPast(); @endphp
                                        <th class="min-w-[80px] px-2 py-2 text-center">
                                            <div class="text-[9px] font-black uppercase tracking-wide {{ $isClosed ? 'text-gray-400' : 'text-blue-500' }}">
                                                {{ $match->phase?->name ? substr($match->phase->name, 0, 3) : '—' }}
                                            </div>
                                            <div class="mt-0.5 font-black text-gray-700">
                                                {{ strtoupper(substr($match->homeTeam?->name ?? '?', 0, 3)) }}
                                                <span class="font-normal text-gray-300">-</span>
                                                {{ strtoupper(substr($match->awayTeam?->name ?? '?', 0, 3)) }}
                                            </div>
                                            <div class="text-[9px] text-gray-400">
                                                {{ $match->starts_at->format('d/m') }}
                                            </div>
                                            @if ($match->status === 'finished')
                                                <div class="mt-0.5 font-black text-gray-800">
                                                    {{ $match->home_score }}–{{ $match->away_score }}
                                                </div>
                                            @endif
                                        </th>
                                    @endforeach
                                    <th class="min-w-[64px] px-2 py-2 text-center text-[9px] font-black uppercase tracking-wide text-gray-400">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($rankings as $ranking)
                                    @php $participantPredictions = $predictions[$ranking->participant_id] ?? collect(); @endphp
                                    <tr class="hover:bg-gray-50/60">
                                        <td class="sticky left-0 z-10 bg-white px-4 py-3">
                                            <p class="font-black text-gray-950">{{ $ranking->participant?->displayName() ?? $ranking->user->name }}</p>
                                            <p class="text-[10px] text-gray-400">#{{ $ranking->position }}</p>
                                        </td>
                                        @foreach ($matches as $match)
                                            @php
                                                $isClosed   = $match->prediction_closes_at->isPast();
                                                $prediction = $participantPredictions[$match->id] ?? null;
                                            @endphp
                                            <td class="px-2 py-3 text-center">
                                                @if (! $isClosed)
                                                    <span class="text-gray-300">·</span>
                                                @elseif ($prediction)
                                                    @php $colorClass = $resultColors[$prediction->result_type] ?? $resultColors['pending']; @endphp
                                                    <span class="inline-block rounded-lg px-2 py-1 font-black {{ $colorClass }}">
                                                        {{ $prediction->predicted_home_score }}-{{ $prediction->predicted_away_score }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-300">—</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="px-2 py-3 text-center font-black text-gray-950">
                                            {{ $ranking->total_points }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Cell legend --}}
                    <div class="mt-3 flex flex-wrap gap-3 text-[10px]">
                        <span class="inline-flex items-center gap-1.5"><span class="rounded-lg bg-green-50 px-2 py-0.5 font-black text-green-800 ring-1 ring-green-200">2-1</span> Exacto</span>
                        <span class="inline-flex items-center gap-1.5"><span class="rounded-lg bg-blue-50 px-2 py-0.5 font-black text-blue-800 ring-1 ring-blue-200">1-0</span> Resultado correcto</span>
                        <span class="inline-flex items-center gap-1.5"><span class="rounded-lg bg-red-50 px-2 py-0.5 font-black text-red-700 ring-1 ring-red-200">0-1</span> Fallo</span>
                        <span class="inline-flex items-center gap-1.5"><span class="rounded-lg bg-gray-50 px-2 py-0.5 font-black text-gray-500 ring-1 ring-gray-200">0-0</span> Pendiente</span>
                        <span class="inline-flex items-center gap-1.5"><span class="font-black text-gray-300">·</span> Partido aún abierto</span>
                        <span class="inline-flex items-center gap-1.5"><span class="font-black text-gray-300">—</span> Sin pronóstico</span>
                    </div>
                </section>
            @endif

        </main>

        <footer class="mt-12 border-t border-gray-200 py-6 text-center text-xs text-gray-400">
            Polla Mundialista 2026 · Todos los pronósticos son visibles para garantizar la transparencia del torneo.
        </footer>

    </div>
</x-app-layout>
