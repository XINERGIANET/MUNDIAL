<?php

namespace App\Support;

use App\Models\FootballMatch;
use App\Models\TournamentParticipant;

class MatchAccess
{
    public static function canParticipantAccess(?TournamentParticipant $participant, FootballMatch $match): bool
    {
        if (! $participant || $participant->tournament_id !== $match->tournament_id) {
            return false;
        }

        if ($participant->isApproved()) {
            return true;
        }

        return $participant->hasCourtesyAccess() && $match->is_welcome_courtesy;
    }
}
