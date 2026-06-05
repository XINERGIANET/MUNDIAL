<?php

namespace App\Filament\Resources\Predictions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PredictionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('tournament_id')->relationship('tournament', 'name')->required()->searchable(),
            Select::make('match_id')->relationship('match', 'id')->required()->searchable(),
            Select::make('user_id')->relationship('user', 'phone')->required()->searchable(),
            TextInput::make('predicted_home_score')->numeric()->required(),
            TextInput::make('predicted_away_score')->numeric()->required(),
            TextInput::make('points_awarded')->numeric()->default(0),
            Select::make('result_type')->options([
                'pending' => 'Pendiente',
                'exact_score' => 'Score exacto',
                'correct_result' => 'Resultado correcto',
                'wrong' => 'Fallo',
            ]),
        ]);
    }
}
