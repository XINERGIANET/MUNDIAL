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
                TextColumn::make('position')->sortable(),
                TextColumn::make('tournament.name')->searchable(),
                TextColumn::make('user.name')->searchable(),
                TextColumn::make('user.phone')->searchable(),
                TextColumn::make('total_points')->sortable(),
                TextColumn::make('exact_scores_count'),
                TextColumn::make('correct_results_count'),
                TextColumn::make('wrong_predictions_count'),
            ])
            ->filters([])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
