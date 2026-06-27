<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class TournamentParticipant extends Model
{
    /** @use HasFactory<\Database\Factories\TournamentParticipantFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tournament_id',
        'user_id',
        'status',
        'payment_status',
        'paid_at',
        'approved_at',
        'predictions_finalized_at',
        'approved_by',
        'notes',
        'entry_name',
        'payment_proof_path',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
            'approved_at' => 'datetime',
            'predictions_finalized_at' => 'datetime',
        ];
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class, 'participant_id');
    }

    public function ranking(): HasOne
    {
        return $this->hasOne(TournamentRanking::class, 'participant_id');
    }

    public function displayName(): string
    {
        return $this->entry_name ?? $this->user->name;
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function hasCourtesyAccess(): bool
    {
        return $this->status === 'pending_payment';
    }

    public function hasFinalizedPredictions(): bool
    {
        return $this->predictions_finalized_at !== null;
    }
}
