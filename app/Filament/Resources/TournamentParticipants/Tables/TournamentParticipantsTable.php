<?php

namespace App\Filament\Resources\TournamentParticipants\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class TournamentParticipantsTable
{
    private static function statusLabel(?string $state): string
    {
        return [
            'pending_payment' => 'Pendiente de pago',
            'approved' => 'Aprobado',
            'rejected' => 'Rechazado',
            'suspended' => 'Suspendido',
        ][$state] ?? (string) $state;
    }

    private static function paymentLabel(?string $state): string
    {
        return [
            'unpaid' => 'No pagado',
            'pending_review' => 'En revision',
            'paid' => 'Pagado',
            'waived' => 'Exonerado',
        ][$state] ?? (string) $state;
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tournament.name')->label('Torneo')->searchable(),
                TextColumn::make('user.name')->label('Usuario')->searchable(),
                TextColumn::make('user.phone')->label('Celular')->searchable(),
                TextColumn::make('status')->label('Estado')->badge()->formatStateUsing(fn (?string $state): string => self::statusLabel($state)),
                TextColumn::make('payment_status')->label('Pago')->badge()->formatStateUsing(fn (?string $state): string => self::paymentLabel($state)),
                ImageColumn::make('payment_proof_path')
                    ->label('Comprobante')
                    ->size(48)
                    ->getStateUsing(fn ($record) => $record->payment_proof_path
                        ? route('participants.proof', $record)
                        : null),
                TextColumn::make('approved_at')->label('Aprobado')->dateTime(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Aprobar')
                    ->action(fn ($record) => $record->update([
                        'status' => 'approved',
                        'payment_status' => $record->payment_status === 'unpaid' ? 'paid' : $record->payment_status,
                        'paid_at' => $record->paid_at ?: now(),
                        'approved_at' => now(),
                        'approved_by' => auth()->id(),
                    ])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
