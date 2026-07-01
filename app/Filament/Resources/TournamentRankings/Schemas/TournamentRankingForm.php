<?php

namespace App\Filament\Resources\TournamentRankings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TournamentRankingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('tournament_id')->label('Torneo')->relationship('tournament', 'name')->required()->searchable(),
            Select::make('user_id')
                ->label('Participante')
                ->relationship('user', 'name')
                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} · {$record->phone}")
                ->required()
                ->searchable(),
            TextInput::make('total_points')->label('Puntos totales')->numeric()->required(),
            TextInput::make('exact_scores_count')->label('Marcadores exactos')->numeric()->required(),
            TextInput::make('correct_results_count')->label('Resultados correctos')->numeric()->required(),
            TextInput::make('wrong_predictions_count')->label('Fallos')->numeric()->required(),
            TextInput::make('predictions_count')->label('Total pronósticos')->numeric()->required(),
            TextInput::make('position')->label('Posición')->numeric()->required(),
        ]);
    }
}
