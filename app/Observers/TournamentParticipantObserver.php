<?php

namespace App\Observers;

use App\Models\TournamentParticipant;

class TournamentParticipantObserver
{
    /**
     * Handle the TournamentParticipant "created" event.
     */
    public function created(TournamentParticipant $tournamentParticipant): void
    {
        //
    }

    /**
     * Handle the TournamentParticipant "updated" event.
     */
    public function updated(TournamentParticipant $tournamentParticipant): void
    {
        //
    }

    // Al hacer soft-delete limpia rankings y predicciones del participante
    public function deleted(TournamentParticipant $participant): void
    {
        $participant->predictions()->delete();
        $participant->ranking()->delete();
    }
}
