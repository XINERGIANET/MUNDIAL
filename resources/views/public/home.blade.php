<x-guest-layout>
    <div class="min-h-screen bg-[#f6f8fb]">
        <header class="border-b border-white/20 wc-shell text-white">
            <div class="max-w-7xl mx-auto px-4 py-5 flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="grid h-11 w-11 place-items-center rounded-lg bg-white text-sm font-black text-blue-800">26</span>
                    <span class="font-black uppercase tracking-wide">Polla Mundialista</span>
                </a>
                <div class="flex items-center gap-4 text-sm font-semibold">
                    <a href="{{ route('login') }}" class="text-white/85 hover:text-white">Iniciar sesion</a>
                    <a href="{{ route('register') }}" class="rounded-md bg-white px-4 py-2 text-blue-800 hover:bg-blue-50">Registrarse</a>
                </div>
            </div>
        </header>

        <main>
            <section class="wc-shell text-white">
                <div class="max-w-7xl mx-auto grid gap-10 px-4 py-14 lg:grid-cols-[1.1fr_.9fr] lg:items-end">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[.2em] text-white/75">Canada · Mexico · USA 2026</p>
                        <h1 class="mt-4 max-w-3xl text-4xl font-black tracking-tight sm:text-6xl">Pronosticos mundialistas con ranking en tiempo real.</h1>
                        <p class="mt-5 max-w-2xl text-lg text-white/80">Gestiona inscripciones, pagos manuales por WhatsApp, resultados oficiales y puntajes de tu polla desde un solo sistema.</p>
                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('register') }}" class="rounded-md bg-white px-6 py-3 text-center text-sm font-black text-blue-800 hover:bg-blue-50">Crear cuenta</a>
                            <a href="{{ route('tournaments.index') }}" class="rounded-md border border-white/30 px-6 py-3 text-center text-sm font-black text-white hover:bg-white/10">Ver torneos</a>
                        </div>
                    </div>

                    <div class="rounded-lg bg-white/10 p-5 ring-1 ring-white/20 backdrop-blur">
                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach ([['Registra', 'Verifica celular y entra al torneo'], ['Paga', 'Coordina por WhatsApp'], ['Pronostica', 'Antes del cierre'], ['Compite', 'Ranking actualizado']] as $item)
                                <div class="rounded-lg bg-white p-4 text-gray-950">
                                    <p class="text-lg font-black">{{ $item[0] }}</p>
                                    <p class="mt-1 text-sm text-gray-500">{{ $item[1] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <section class="max-w-7xl mx-auto px-4 py-10">
                <div class="mb-5 flex items-end justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-wide text-red-600">Competencia</p>
                        <h2 class="text-2xl font-black text-gray-950">Torneos disponibles</h2>
                    </div>
                </div>
                <div class="grid gap-4 md:grid-cols-3">
                    @forelse ($tournaments as $tournament)
                        <a href="{{ route('tournaments.ranking', $tournament) }}" class="wc-card overflow-hidden rounded-lg hover:-translate-y-0.5 hover:border-blue-300 transition">
                            <div class="wc-accent-line"></div>
                            <div class="p-5">
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 ring-1 ring-blue-200">{{ $tournament->status }}</span>
                                <h3 class="mt-4 text-lg font-black text-gray-950">{{ $tournament->name }}</h3>
                                <p class="mt-1 text-sm text-gray-500">Inicio {{ $tournament->starts_at->format('d/m/Y') }}</p>
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-gray-500">No hay torneos disponibles.</p>
                    @endforelse
                </div>
            </section>
        </main>
    </div>
</x-guest-layout>
