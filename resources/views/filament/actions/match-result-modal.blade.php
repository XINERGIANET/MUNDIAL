<div style="margin-bottom:1.25rem;overflow:hidden;border-radius:1rem;background:#030712;">

    {{-- Equipos --}}
    <div style="display:flex;align-items:center;gap:0.75rem;padding:1.25rem 1rem;">

        {{-- Local --}}
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:0.5rem;text-align:center;">
            @if ($record->homeTeam->logo_path)
                <img src="{{ $record->homeTeam->logo_path }}"
                     alt="{{ $record->homeTeam->name }}"
                     style="height:4rem;width:6rem;border-radius:0.75rem;object-fit:contain;border:1px solid rgba(255,255,255,.1);">
            @else
                <div style="height:4rem;width:6rem;border-radius:0.75rem;background:rgba(255,255,255,.05);display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:rgba(255,255,255,.2);">?</div>
            @endif
            <div>
                <p style="font-weight:900;color:#fff;line-height:1.2;">{{ $record->homeTeam->name }}</p>
                <p style="font-size:0.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#6b7280;margin-top:2px;">Local</p>
            </div>
        </div>

        {{-- Centro --}}
        <div style="flex-shrink:0;text-align:center;">
            <div style="border:1px solid rgba(255,255,255,.1);border-radius:0.75rem;padding:0.75rem 1rem;">
                <p style="font-size:1rem;font-weight:900;color:rgba(255,255,255,.25);">VS</p>
            </div>
            @if ($record->phase?->name)
                <p style="font-size:0.55rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#4b5563;margin-top:6px;">{{ $record->phase->name }}</p>
            @endif
        </div>

        {{-- Visitante --}}
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:0.5rem;text-align:center;">
            @if ($record->awayTeam->logo_path)
                <img src="{{ $record->awayTeam->logo_path }}"
                     alt="{{ $record->awayTeam->name }}"
                     style="height:4rem;width:6rem;border-radius:0.75rem;object-fit:contain;border:1px solid rgba(255,255,255,.1);">
            @else
                <div style="height:4rem;width:6rem;border-radius:0.75rem;background:rgba(255,255,255,.05);display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:rgba(255,255,255,.2);">?</div>
            @endif
            <div>
                <p style="font-weight:900;color:#fff;line-height:1.2;">{{ $record->awayTeam->name }}</p>
                <p style="font-size:0.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#6b7280;margin-top:2px;">Visitante</p>
            </div>
        </div>

    </div>

    {{-- Nota --}}
    <div style="border-top:1px solid rgba(255,255,255,.06);background:rgba(255,255,255,.04);padding:0.6rem 1rem;">
        <p style="font-size:0.7rem;color:#9ca3af;line-height:1.4;">
            Ingresa el marcador al final del tiempo reglamentario (+ tiempo extra si aplica).
            Si hay empate, aparecerá el selector del ganador en penales.
        </p>
    </div>

</div>
