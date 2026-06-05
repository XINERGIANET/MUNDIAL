<div class="space-y-5">
    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
        <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-4">
            <div class="text-center">
                @if ($record->homeTeam->logo_path)
                    <img src="{{ $record->homeTeam->logo_path }}" alt="{{ $record->homeTeam->name }}" class="mx-auto mb-3 h-14 w-20 rounded object-cover ring-1 ring-gray-200 dark:ring-gray-700">
                @endif
                <p class="text-base font-black text-gray-950 dark:text-white">{{ $record->homeTeam->name }}</p>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Local</p>
            </div>

            <div class="rounded-full bg-white px-4 py-2 text-sm font-black text-gray-500 ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">VS</div>

            <div class="text-center">
                @if ($record->awayTeam->logo_path)
                    <img src="{{ $record->awayTeam->logo_path }}" alt="{{ $record->awayTeam->name }}" class="mx-auto mb-3 h-14 w-20 rounded object-cover ring-1 ring-gray-200 dark:ring-gray-700">
                @endif
                <p class="text-base font-black text-gray-950 dark:text-white">{{ $record->awayTeam->name }}</p>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Visitante</p>
            </div>
        </div>
    </div>

    <p class="text-sm text-gray-600 dark:text-gray-300">
        Ingresa solo el marcador oficial final. Al guardar, se recalculan automaticamente los puntos y el ranking del torneo.
    </p>
</div>
