<?php

namespace App\Filament\Resources\TournamentRankings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TournamentRankingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('position')->label('Posicion')->sortable(),
                TextColumn::make('tournament.name')->label('Torneo')->searchable(),
                TextColumn::make('user.name')->label('Usuario')->searchable(),
                TextColumn::make('user.phone')->label('Celular')->searchable(),
                TextColumn::make('total_points')->label('Puntos')->sortable(),
                TextColumn::make('exact_scores_count')->label('Scores exactos'),
                TextColumn::make('correct_results_count')->label('Resultados correctos'),
                TextColumn::make('wrong_predictions_count')->label('Fallos'),
            ])
            ->filters([])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
