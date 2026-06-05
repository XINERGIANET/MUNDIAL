<x-guest-layout>
    <div class="min-h-screen bg-gray-50">
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
                <a href="{{ route('home') }}" class="font-bold text-xl text-blue-700">Sistema de Polla Mundialista</a>
                <div class="flex gap-3">
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-blue-700">Iniciar sesion</a>
                    <a href="{{ route('register') }}" class="text-sm font-medium text-blue-700 hover:text-blue-900">Registrarse</a>
                </div>
            </div>
        </header>
        <main class="max-w-7xl mx-auto px-4 py-10 space-y-10">
            <section class="grid gap-8 lg:grid-cols-[1.1fr_.9fr] lg:items-center">
                <div>
                    <h1 class="text-4xl font-bold tracking-tight text-gray-950 sm:text-5xl">Sistema de Polla Mundialista</h1>
                    <p class="mt-4 text-lg text-gray-600">Registra pronosticos, suma puntos y sigue el ranking de tus torneos favoritos.</p>
                    <div class="mt-6 flex gap-3">
                        <a href="{{ route('register') }}" class="rounded-md bg-blue-700 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-800">Crear cuenta</a>
                        <a href="{{ route('tournaments.index') }}" class="rounded-md border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-800 hover:bg-white">Ver torneos</a>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-5">
                    <h2 class="font-semibold text-gray-900">Como funciona</h2>
                    <ol class="mt-4 space-y-2 text-sm text-gray-600">
                        <li>1. Registrate y verifica tu celular.</li>
                        <li>2. Inscribete al torneo.</li>
                        <li>3. Coordina el pago por WhatsApp.</li>
                        <li>4. Pronostica antes del cierre.</li>
                        <li>5. Suma puntos y mira el ranking.</li>
                    </ol>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Torneos disponibles</h2>
                <div class="grid gap-4 md:grid-cols-3">
                    @foreach ($tournaments as $tournament)
                        <a href="{{ route('tournaments.ranking', $tournament) }}" class="bg-white border border-gray-200 rounded-lg p-5 hover:border-blue-300">
                            <h3 class="font-semibold text-gray-900">{{ $tournament->name }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ ucfirst($tournament->status) }} · {{ $tournament->starts_at->format('d/m/Y') }}</p>
                        </a>
                    @endforeach
                </div>
            </section>
        </main>
    </div>
</x-guest-layout>
