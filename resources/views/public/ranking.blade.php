<x-guest-layout>
    <div class="min-h-screen bg-[#f6f8fb] px-4 py-10">
        <div class="max-w-5xl mx-auto">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-red-600">Tabla general</p>
                    <h1 class="text-3xl font-black text-gray-950">Ranking {{ $tournament->name }}</h1>
                </div>
                <a href="{{ route('home') }}" class="text-sm font-medium text-blue-700">Inicio</a>
            </div>
            <div class="wc-card overflow-hidden rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Pos</th>
                            <th class="px-4 py-3">Usuario</th>
                            <th class="px-4 py-3">Celular</th>
                            <th class="px-4 py-3">Pts</th>
                            <th class="px-4 py-3">Exactos</th>
                            <th class="px-4 py-3">Correctos</th>
                            <th class="px-4 py-3">Fallos</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($rankings as $ranking)
                            <tr>
                                <td class="px-4 py-3 font-semibold">#{{ $ranking->position }}</td>
                                <td class="px-4 py-3">{{ $ranking->user->name }}</td>
                                <td class="px-4 py-3">{{ $ranking->user->maskedPhone() }}</td>
                                <td class="px-4 py-3 font-semibold">{{ $ranking->total_points }}</td>
                                <td class="px-4 py-3">{{ $ranking->exact_scores_count }}</td>
                                <td class="px-4 py-3">{{ $ranking->correct_results_count }}</td>
                                <td class="px-4 py-3">{{ $ranking->wrong_predictions_count }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-6 text-center text-gray-500">Aun no hay ranking calculado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-guest-layout>
