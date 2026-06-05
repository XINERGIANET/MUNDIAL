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
            Select::make('tournament_id')->relationship('tournament', 'name')->required()->searchable(),
            Select::make('user_id')->relationship('user', 'phone')->required()->searchable(),
            TextInput::make('total_points')->numeric()->required(),
            TextInput::make('exact_scores_count')->numeric()->required(),
            TextInput::make('correct_results_count')->numeric()->required(),
            TextInput::make('wrong_predictions_count')->numeric()->required(),
            TextInput::make('predictions_count')->numeric()->required(),
            TextInput::make('position')->numeric()->required(),
        ]);
    }
}
