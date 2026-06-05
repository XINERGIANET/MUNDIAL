<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTournamentParticipantIsApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tournament = $request->route('tournament') ?: $request->route('match')?->tournament;

        abort_unless($tournament, 404);

        $approved = $request->user()?->participants()
            ->where('tournament_id', $tournament->id)
            ->where('status', 'approved')
            ->exists();

        abort_unless($approved, 403);

        return $next($request);
    }
}
