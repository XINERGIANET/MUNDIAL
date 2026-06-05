<?php

namespace App\Filament\Resources\TournamentParticipants\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class TournamentParticipantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tournament.name')->searchable(),
                TextColumn::make('user.name')->searchable(),
                TextColumn::make('user.phone')->searchable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('payment_status')->badge(),
                TextColumn::make('approved_at')->dateTime(),
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
