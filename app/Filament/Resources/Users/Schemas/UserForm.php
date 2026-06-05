<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('phone')->required()->maxLength(30),
            TextInput::make('phone_normalized')->required()->maxLength(30),
            TextInput::make('email')->email()->maxLength(255),
            DateTimePicker::make('phone_verified_at'),
            Toggle::make('is_active')->default(true),
            TextInput::make('password')->password()->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)->dehydrated(fn ($state) => filled($state)),
            Select::make('roles')->relationship('roles', 'name')->multiple()->preload(),
        ]);
    }
}
