<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prediction extends Model
{
    /** @use HasFactory<\Database\Factories\PredictionFactory> */
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'match_id',
        'user_id',
        'predicted_home_score',
        'predicted_away_score',
        'points_awarded',
        'result_type',
        'locked_at',
        'calculated_at',
    ];

    protected function casts(): array
    {
        return ['locked_at' => 'datetime', 'calculated_at' => 'datetime'];
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(FootballMatch::class, 'match_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
