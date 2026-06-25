<?php

namespace App\Filament\Resources\FootballMatches\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FootballMatchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tournament_id')->label('Torneo')->relationship('tournament', 'name')->required()->searchable(),
                Select::make('phase_id')->label('Fase')->relationship('phase', 'name')->required()->searchable(),
                Select::make('group_id')->label('Grupo')->relationship('group', 'name')->searchable(),
                Select::make('home_team_id')->label('Equipo local')->relationship('homeTeam', 'name')->required()->searchable(),
                Select::make('away_team_id')->label('Equipo visitante')->relationship('awayTeam', 'name')->required()->searchable()->different('home_team_id'),
                DateTimePicker::make('starts_at')->label('Inicio')->required(),
                DateTimePicker::make('prediction_closes_at')->label('Cierre de pronosticos')->required(),
                Select::make('status')->label('Estado')->options([
                    'scheduled' => 'Programado',
                    'live' => 'En vivo',
                    'finished' => 'Finalizado',
                    'cancelled' => 'Cancelado',
                ])->required(),
                Toggle::make('is_welcome_courtesy')
                    ->label('Cortesia de bienvenida')
                    ->helperText('Permite que usuarios pendientes de pago puedan ver y pronosticar este partido.')
                    ->default(false),
                TextInput::make('home_score')->label('Goles local')->numeric()->minValue(0)->maxValue(30),
                TextInput::make('away_score')->label('Goles visitante')->numeric()->minValue(0)->maxValue(30),
            ]);
    }
}
