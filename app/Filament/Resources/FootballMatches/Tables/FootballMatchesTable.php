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
use Illuminate\Support\Facades\Auth;

class FootballMatchesTable
{
    private static function statusLabel(?string $state): string
    {
        return [
            'scheduled' => 'Programado',
            'live' => 'En vivo',
            'finished' => 'Finalizado',
            'cancelled' => 'Cancelado',
        ][$state] ?? (string) $state;
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tournament.name')->label('Torneo')->searchable(),
                TextColumn::make('homeTeam.name')->label('Local')->searchable(),
                TextColumn::make('awayTeam.name')->label('Visitante')->searchable(),
                TextColumn::make('starts_at')->label('Inicio')->dateTime()->sortable(),
                TextColumn::make('prediction_closes_at')->label('Cierre pronosticos')->dateTime()->sortable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => self::statusLabel($state))
                    ->color(fn (?string $state): string => match ($state) {
                        'finished' => 'success',
                        'live' => 'warning',
                        'cancelled' => 'danger',
                        default => 'info',
                    }),
                TextColumn::make('score')->label('Marcador')->state(fn ($record) => $record->home_score === null ? '-' : $record->home_score.' - '.$record->away_score),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('result')
                    ->label('Registrar resultado')
                    ->modalHeading('Registrar resultado oficial')
                    ->modalWidth('lg')
                    ->modalSubmitActionLabel('Guardar resultado')
                    ->modalContent(fn ($record) => view('filament.actions.match-result-modal', ['record' => $record]))
                    ->schema([
                        TextInput::make('home_score')
                            ->label('Goles local')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(30)
                            ->extraInputAttributes(['class' => 'text-center text-2xl font-black']),
                        TextInput::make('away_score')
                            ->label('Goles visitante')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(30)
                            ->extraInputAttributes(['class' => 'text-center text-2xl font-black']),
                    ])
                    ->action(function ($record, array $data): void {
                        $admin = Auth::user();

                        if (! $admin) {
                            return;
                        }

                        app(MatchResultService::class)->register($record, (int) $data['home_score'], (int) $data['away_score'], $admin);
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
