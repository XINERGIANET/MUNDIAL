<?php

namespace App\Filament\Resources\TournamentPhases\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TournamentPhaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tournament_id')->relationship('tournament', 'name')->required()->searchable(),
                TextInput::make('name')->required()->maxLength(255),
                Select::make('type')->options([
                    'group_stage' => 'Fase de grupos',
                    'knockout' => 'Eliminatoria',
                    'final' => 'Final',
                    'custom' => 'Personalizada',
                ])->required(),
                TextInput::make('order')->numeric()->default(1)->required(),
                DateTimePicker::make('starts_at'),
                DateTimePicker::make('ends_at'),
                Toggle::make('is_active')->default(true),
            ]);
    }
}
