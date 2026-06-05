<?php

namespace App\Filament\Resources\Teams\Schemas;

use Filament\Forms\Components\FileUpload;
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
                TextInput::make('name')->required()->unique(ignoreRecord: true)->maxLength(255),
                TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(255),
                FileUpload::make('logo_path')->image()->directory('teams')->disk('public'),
                TextInput::make('fifa_code')->maxLength(8),
                TextInput::make('country')->maxLength(255),
                Textarea::make('description')->columnSpanFull(),
                Toggle::make('is_active')->default(true),
            ]);
    }
}
