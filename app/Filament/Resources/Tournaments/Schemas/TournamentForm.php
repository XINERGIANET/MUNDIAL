<?php

namespace App\Filament\Resources\Tournaments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TournamentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(255),
                FileUpload::make('banner_path')->image()->directory('tournaments')->disk('public'),
                DateTimePicker::make('starts_at')->required(),
                DateTimePicker::make('ends_at')->required(),
                Select::make('status')->options([
                    'draft' => 'Borrador',
                    'open' => 'Abierto',
                    'running' => 'En curso',
                    'finished' => 'Finalizado',
                    'cancelled' => 'Cancelado',
                ])->required(),
                TextInput::make('entry_fee')->numeric()->prefix('S/'),
                TextInput::make('currency')->default('PEN')->required()->maxLength(8),
                TextInput::make('payment_whatsapp_number')->maxLength(30),
                TextInput::make('exact_score_points')->numeric()->default(5)->required(),
                TextInput::make('correct_result_points')->numeric()->default(3)->required(),
                TextInput::make('wrong_prediction_points')->numeric()->default(0)->required(),
                Toggle::make('is_active')->default(true),
                Textarea::make('payment_message')->columnSpanFull(),
                Textarea::make('description')->columnSpanFull(),
                Textarea::make('rules')->columnSpanFull(),
            ]);
    }
}
