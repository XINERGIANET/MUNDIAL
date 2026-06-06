<x-filament-panels::page>
    <div class="space-y-4">
        <div class="flex flex-col gap-3 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-black text-gray-950 dark:text-white">Registrar resultados</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Completa las casillas y guarda todos los marcadores en una sola accion.</p>
            </div>

            <div class="flex flex-col gap-2 sm:flex-row">
                <button type="button" wire:click="randomize" class="rounded-lg border border-amber-300 px-4 py-2 text-sm font-bold text-amber-700 hover:bg-amber-50 dark:border-amber-700 dark:text-amber-300 dark:hover:bg-amber-950">
                    Generar aleatorios
                </button>
                <button type="button" wire:click="save" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-bold text-white hover:bg-primary-700">
                    Guardar resultados
                </button>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-950">
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach ($this->matches() as $match)
                    <article class="grid gap-4 p-4 xl:grid-cols-[220px_1fr] xl:items-center">
                        <div>
                            <p class="text-sm font-black text-gray-950 dark:text-white">{{ $match->starts_at->format('d/m/Y') }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $match->starts_at->format('H:i') }} · {{ $match->tournament?->name }}</p>
                            <span class="mt-2 inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700 ring-1 ring-blue-200 dark:bg-blue-950 dark:text-blue-200 dark:ring-blue-800">
                                {{ $match->group?->name ?? $match->phase?->name ?? 'Sin grupo' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-[minmax(0,1fr)_4rem_2.5rem_4rem_minmax(0,1fr)] items-start gap-2 xl:grid-cols-[minmax(220px,1fr)_4.5rem_3rem_4.5rem_minmax(220px,1fr)] xl:items-center xl:gap-4">
                            <div class="min-w-0 text-center xl:flex xl:items-center xl:gap-3 xl:text-left">
                                @if ($match->homeTeam?->logo_path)
                                    <img src="{{ $match->homeTeam->logo_path }}" alt="{{ $match->homeTeam->name }}" class="mx-auto h-10 w-14 shrink-0 rounded object-cover ring-1 ring-gray-200 xl:mx-0">
                                @endif
                                <div class="min-w-0">
                                    <p class="mt-1 truncate text-sm font-black text-gray-950 dark:text-white xl:mt-0 xl:text-base">{{ $match->homeTeam?->name }}</p>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 xl:text-xs">Local</p>
                                </div>
                            </div>

                            <input type="number" min="0" max="30" wire:model="scores.{{ $match->id }}.home_score" class="h-12 w-full rounded-lg border-gray-300 bg-white text-center text-xl font-black text-gray-950 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">

                            <div class="grid h-11 place-items-center rounded-lg bg-gray-100 px-2 text-sm font-black text-gray-500 dark:bg-gray-800 dark:text-gray-300">VS</div>

                            <input type="number" min="0" max="30" wire:model="scores.{{ $match->id }}.away_score" class="h-12 w-full rounded-lg border-gray-300 bg-white text-center text-xl font-black text-gray-950 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">

                            <div class="flex min-w-0 flex-col items-center text-center xl:flex-row xl:justify-end xl:gap-3 xl:text-right">
                                <div class="min-w-0">
                                    <p class="mt-1 truncate text-sm font-black text-gray-950 dark:text-white xl:mt-0 xl:text-base">{{ $match->awayTeam?->name }}</p>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 xl:text-xs">Visitante</p>
                                </div>
                                @if ($match->awayTeam?->logo_path)
                                    <img src="{{ $match->awayTeam->logo_path }}" alt="{{ $match->awayTeam->name }}" class="order-first mx-auto h-10 w-14 shrink-0 rounded object-cover ring-1 ring-gray-200 xl:order-none xl:mx-0">
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

        <div class="sticky bottom-4 z-10 flex justify-end rounded-xl border border-gray-200 bg-white/95 p-4 shadow-lg backdrop-blur dark:border-gray-800 dark:bg-gray-950/95">
            <button type="button" wire:click="save" class="rounded-lg bg-primary-600 px-5 py-3 text-sm font-bold text-white hover:bg-primary-700">
                Guardar resultados
            </button>
        </div>
    </div>
</x-filament-panels::page>
