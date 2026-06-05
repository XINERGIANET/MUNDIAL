<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('key')->required()->unique(ignoreRecord: true),
            Textarea::make('value')->columnSpanFull(),
            Select::make('type')->options(['string' => 'Texto', 'integer' => 'Entero', 'boolean' => 'Booleano', 'json' => 'JSON'])->required(),
            TextInput::make('group')->default('general')->required(),
            Toggle::make('is_public')->default(false),
        ]);
    }
}
