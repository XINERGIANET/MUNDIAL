<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TournamentRanking extends Model
{
    /** @use HasFactory<\Database\Factories\TournamentRankingFactory> */
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'user_id',
        'participant_id',
        'total_points',
        'exact_scores_count',
        'correct_results_count',
        'wrong_predictions_count',
        'predictions_count',
        'position',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(TournamentParticipant::class, 'participant_id');
    }
}
