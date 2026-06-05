<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tournament extends Model
{
    /** @use HasFactory<\Database\Factories\TournamentFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'banner_path',
        'starts_at',
        'ends_at',
        'status',
        'entry_fee',
        'currency',
        'payment_whatsapp_number',
        'payment_message',
        'rules',
        'exact_score_points',
        'correct_result_points',
        'wrong_prediction_points',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'entry_fee' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function phases(): HasMany
    {
        return $this->hasMany(TournamentPhase::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(TournamentGroup::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(FootballMatch::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(TournamentParticipant::class);
    }

    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class);
    }

    public function rankings(): HasMany
    {
        return $this->hasMany(TournamentRanking::class);
    }

    public function whatsappPaymentUrl(User $user): ?string
    {
        $number = preg_replace('/\D+/', '', $this->payment_whatsapp_number ?: config('polla.payment_whatsapp_number'));

        if (! $number) {
            return null;
        }

        $message = $this->payment_message ?: config('polla.payment_message');
        $message = strtr($message, [
            '{nombre}' => $user->name,
            '{celular}' => $user->phone,
            '{torneo}' => $this->name,
            '{monto}' => $this->entry_fee ? $this->currency.' '.$this->entry_fee : '',
        ]);

        return 'https://wa.me/'.$number.'?text='.rawurlencode($message);
    }
}
