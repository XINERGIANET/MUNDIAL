<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FootballMatch extends Model
{
    /** @use HasFactory<\Database\Factories\FootballMatchFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'matches';

    protected $fillable = [
        'tournament_id',
        'phase_id',
        'group_id',
        'home_source_match_id',
        'away_source_match_id',
        'home_team_id',
        'away_team_id',
        'starts_at',
        'prediction_closes_at',
        'status',
        'is_welcome_courtesy',
        'home_score',
        'away_score',
        'result_registered_by',
        'result_registered_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'prediction_closes_at' => 'datetime',
            'result_registered_at' => 'datetime',
            'is_welcome_courtesy' => 'boolean',
        ];
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(TournamentPhase::class, 'phase_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(TournamentGroup::class, 'group_id');
    }

    public function homeSourceMatch(): BelongsTo
    {
        return $this->belongsTo(FootballMatch::class, 'home_source_match_id');
    }

    public function awaySourceMatch(): BelongsTo
    {
        return $this->belongsTo(FootballMatch::class, 'away_source_match_id');
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class, 'match_id');
    }

    public function resultRegisteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'result_registered_by');
    }

    public function isPredictionOpen(): bool
    {
        return $this->status !== 'cancelled' && now()->lt($this->prediction_closes_at);
    }

    public function hasOfficialResult(): bool
    {
        return $this->home_score !== null && $this->away_score !== null;
    }
}
