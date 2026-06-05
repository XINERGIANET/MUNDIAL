<?php

namespace App\Filament\Resources\TournamentParticipants\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TournamentParticipantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tournament_id')->label('Torneo')->relationship('tournament', 'name')->required()->searchable(),
                Select::make('user_id')->label('Usuario')->relationship('user', 'phone')->required()->searchable(),
                Select::make('status')->label('Estado')->options([
                    'pending_payment' => 'Pendiente de pago',
                    'approved' => 'Aprobado',
                    'rejected' => 'Rechazado',
                    'suspended' => 'Suspendido',
                ])->required(),
                Select::make('payment_status')->label('Estado de pago')->options([
                    'unpaid' => 'No pagado',
                    'pending_review' => 'Revision pendiente',
                    'paid' => 'Pagado',
                    'waived' => 'Exonerado',
                ])->required(),
                DateTimePicker::make('paid_at')->label('Fecha de pago'),
                DateTimePicker::make('approved_at')->label('Fecha de aprobacion'),
                Select::make('approved_by')->label('Aprobado por')->relationship('approver', 'name')->searchable(),
                Textarea::make('notes')->label('Notas')->columnSpanFull(),
            ]);
    }
}
