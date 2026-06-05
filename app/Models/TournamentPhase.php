<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TournamentPhase extends Model
{
    /** @use HasFactory<\Database\Factories\TournamentPhaseFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['tournament_id', 'name', 'type', 'order', 'starts_at', 'ends_at', 'is_active'];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'is_active' => 'boolean'];
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(TournamentGroup::class, 'phase_id');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(FootballMatch::class, 'phase_id');
    }
}
