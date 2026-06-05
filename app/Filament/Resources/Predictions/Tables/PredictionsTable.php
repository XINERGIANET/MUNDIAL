<?php

namespace App\Filament\Resources\Predictions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PredictionsTable
{
    private static function resultLabel(?string $state): string
    {
        return [
            'pending' => 'Pendiente',
            'exact_score' => 'Score exacto',
            'correct_result' => 'Resultado correcto',
            'wrong' => 'Fallo',
        ][$state] ?? (string) $state;
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tournament.name')->label('Torneo')->searchable(),
                TextColumn::make('user.name')->label('Usuario')->searchable(),
                TextColumn::make('match.id')->label('Partido'),
                TextColumn::make('predicted_home_score')->label('Goles local'),
                TextColumn::make('predicted_away_score')->label('Goles visitante'),
                TextColumn::make('points_awarded')->label('Puntos')->sortable(),
                TextColumn::make('result_type')->label('Resultado')->badge()->formatStateUsing(fn (?string $state): string => self::resultLabel($state)),
            ])
            ->filters([])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
