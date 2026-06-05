<?php

namespace App\Filament\Resources\Predictions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PredictionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tournament.name')->searchable(),
                TextColumn::make('user.name')->searchable(),
                TextColumn::make('match.id')->label('Partido'),
                TextColumn::make('predicted_home_score'),
                TextColumn::make('predicted_away_score'),
                TextColumn::make('points_awarded')->sortable(),
                TextColumn::make('result_type')->badge(),
            ])
            ->filters([])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
