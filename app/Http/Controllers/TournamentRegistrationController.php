<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentParticipant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TournamentRegistrationController extends Controller
{
    public function store(Request $request, Tournament $tournament): RedirectResponse
    {
        abort_unless($request->user()->hasVerifiedPhone(), 403);

        TournamentParticipant::firstOrCreate(
            ['tournament_id' => $tournament->id, 'user_id' => $request->user()->id],
            ['status' => 'pending_payment', 'payment_status' => 'unpaid']
        );

        return redirect()->route('dashboard')->with('status', 'Inscripcion creada. Solicita los medios de pago por WhatsApp.');
    }
}
