<?php

namespace App\Observers;

use App\Models\TournamentParticipant;
use App\Services\RankingService;

class TournamentParticipantObserver
{
    public function saved(TournamentParticipant $participant): void
    {
        if ($participant->status === 'approved') {
            app(RankingService::class)->recalculateTournamentRanking(
                $participant->tournament
            );
        }
    }

    // Al hacer soft-delete limpia rankings y predicciones del participante
    public function deleted(TournamentParticipant $participant): void
    {
        $participant->predictions()->delete();
        $participant->ranking()->delete();
    }
}
