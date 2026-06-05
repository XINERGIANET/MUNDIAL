<x-guest-layout>
    <div class="min-h-screen bg-gray-50 px-4 py-10">
        <div class="max-w-5xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-950">Torneos</h1>
            <div class="mt-6 grid gap-4 md:grid-cols-2">
                @foreach ($tournaments as $tournament)
                    <a href="{{ route('tournaments.ranking', $tournament) }}" class="bg-white border border-gray-200 rounded-lg p-5">
                        <h2 class="font-semibold text-gray-900">{{ $tournament->name }}</h2>
                        <p class="text-sm text-gray-500">{{ $tournament->status }} · {{ $tournament->currency }} {{ $tournament->entry_fee ?? '0.00' }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</x-guest-layout>
