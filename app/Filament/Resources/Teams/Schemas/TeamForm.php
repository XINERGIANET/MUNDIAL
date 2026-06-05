<?php

namespace App\Filament\Resources\Teams\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TeamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Nombre')->required()->unique(ignoreRecord: true)->maxLength(255),
                TextInput::make('slug')->label('Slug')->required()->unique(ignoreRecord: true)->maxLength(255),
                TextInput::make('logo_path')->label('URL bandera/logo')->url()->maxLength(255),
                TextInput::make('fifa_code')->label('Codigo FIFA')->maxLength(8),
                TextInput::make('country')->label('Pais')->maxLength(255),
                Textarea::make('description')->label('Descripcion')->columnSpanFull(),
                Toggle::make('is_active')->label('Activo')->default(true),
            ]);
    }
}
