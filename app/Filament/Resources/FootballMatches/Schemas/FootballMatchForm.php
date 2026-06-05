<?php

namespace App\Filament\Resources\FootballMatches\Schemas;

use App\Services\AuditService;
use App\Services\RankingService;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FootballMatchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tournament_id')->relationship('tournament', 'name')->required()->searchable(),
                Select::make('phase_id')->relationship('phase', 'name')->required()->searchable(),
                Select::make('group_id')->relationship('group', 'name')->searchable(),
                Select::make('home_team_id')->relationship('homeTeam', 'name')->required()->searchable(),
                Select::make('away_team_id')->relationship('awayTeam', 'name')->required()->searchable()->different('home_team_id'),
                DateTimePicker::make('starts_at')->required(),
                DateTimePicker::make('prediction_closes_at')->required(),
                Select::make('status')->options([
                    'scheduled' => 'Programado',
                    'live' => 'En vivo',
                    'finished' => 'Finalizado',
                    'cancelled' => 'Cancelado',
                ])->required(),
                TextInput::make('home_score')->numeric()->minValue(0)->maxValue(30),
                TextInput::make('away_score')->numeric()->minValue(0)->maxValue(30),
            ]);
    }
}
