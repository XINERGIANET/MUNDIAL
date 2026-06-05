<?php

namespace App\Filament\Resources\FootballMatches\Tables;

use App\Services\MatchResultService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class FootballMatchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tournament.name')->searchable(),
                TextColumn::make('homeTeam.name')->label('Local')->searchable(),
                TextColumn::make('awayTeam.name')->label('Visitante')->searchable(),
                TextColumn::make('starts_at')->dateTime()->sortable(),
                TextColumn::make('prediction_closes_at')->dateTime()->sortable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('score')->state(fn ($record) => $record->home_score === null ? '-' : $record->home_score.' - '.$record->away_score),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('result')
                    ->label('Registrar resultado')
                    ->schema([
                        TextInput::make('home_score')->numeric()->required()->minValue(0)->maxValue(30),
                        TextInput::make('away_score')->numeric()->required()->minValue(0)->maxValue(30),
                    ])
                    ->action(function ($record, array $data): void {
                        app(MatchResultService::class)->register($record, (int) $data['home_score'], (int) $data['away_score'], auth()->user());
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
