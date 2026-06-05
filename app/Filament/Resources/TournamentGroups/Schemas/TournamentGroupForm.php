<?php

namespace App\Filament\Resources\TournamentGroups\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TournamentGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tournament_id')->relationship('tournament', 'name')->required()->searchable(),
                Select::make('phase_id')->relationship('phase', 'name')->required()->searchable(),
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('order')->numeric()->default(1)->required(),
                Select::make('teams')->relationship('teams', 'name')->multiple()->preload()->searchable()->columnSpanFull(),
            ]);
    }
}
