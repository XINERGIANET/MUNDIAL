<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentParticipant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TournamentRegistrationController extends Controller
{
    public function store(Request $request, Tournament $tournament): RedirectResponse
    {
        abort_unless($request->user()->hasVerifiedPhone(), 403);

        $validated = $request->validate([
            'entry_name'    => ['nullable', 'string', 'max:60'],
            'payment_proof' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:8192'],
        ]);

        $path = $request->file('payment_proof')->store('payment-proofs', 'public');

        TournamentParticipant::create([
            'tournament_id'     => $tournament->id,
            'user_id'           => $request->user()->id,
            'entry_name'        => $validated['entry_name'] ?: null,
            'payment_proof_path' => $path,
            'status'            => 'pending_payment',
            'payment_status'    => 'pending_review',
        ]);

        return redirect()->route('dashboard')->with('status', 'Inscripción enviada. Estamos verificando tu pago, te avisaremos cuando esté aprobado.');
    }

    public function serveProof(TournamentParticipant $participant): BinaryFileResponse
    {
        abort_unless($participant->payment_proof_path, 404);

        $absolutePath = Storage::disk('public')->path($participant->payment_proof_path);
        abort_unless(file_exists($absolutePath), 404);

        return response()->file($absolutePath);
    }
}
