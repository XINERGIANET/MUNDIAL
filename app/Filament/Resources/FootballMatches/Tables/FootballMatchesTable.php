<?php

namespace App\Filament\Resources\FootballMatches\Tables;

use App\Services\MatchResultService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
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
                ToggleColumn::make('is_welcome_courtesy')->label('Cortesia'),
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
                TextColumn::make('score')->label('Marcador')->state(function ($record) {
                    if ($record->home_score === null) {
                        return '-';
                    }
                    $score = $record->home_score.' - '.$record->away_score;
                    if ($record->penalty_winner_team_id) {
                        $winner = $record->home_team_id === $record->penalty_winner_team_id
                            ? $record->homeTeam?->name
                            : $record->awayTeam?->name;
                        $score .= ' (P: '.$winner.')';
                    }
                    return $score;
                }),
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
                        Grid::make(2)->schema([
                            TextInput::make('home_score')
                                ->label('Goles local')
                                ->numeric()
                                ->required()
                                ->live()
                                ->minValue(0)
                                ->maxValue(30)
                                ->placeholder('0')
                                ->extraInputAttributes(['class' => 'text-center text-3xl font-black h-16']),
                            TextInput::make('away_score')
                                ->label('Goles visitante')
                                ->numeric()
                                ->required()
                                ->live()
                                ->minValue(0)
                                ->maxValue(30)
                                ->placeholder('0')
                                ->extraInputAttributes(['class' => 'text-center text-3xl font-black h-16']),
                        ]),
                        Select::make('penalty_winner_team_id')
                            ->label('Ganador en penales')
                            ->helperText('El marcador quedó en empate — selecciona el equipo que ganó en la tanda de penales.')
                            ->options(fn ($record) => [
                                $record->home_team_id => $record->homeTeam?->name ?? 'Local',
                                $record->away_team_id => $record->awayTeam?->name ?? 'Visitante',
                            ])
                            ->required()
                            ->hidden(fn (Get $get) => (string) $get('home_score') !== (string) $get('away_score')
                                || $get('home_score') === null
                                || $get('home_score') === ''),
                    ])
                    ->action(function ($record, array $data): void {
                        $admin = Auth::user();

                        if (! $admin) {
                            return;
                        }

                        $penaltyWinnerId = isset($data['penalty_winner_team_id'])
                            ? (int) $data['penalty_winner_team_id']
                            : null;

                        app(MatchResultService::class)->register(
                            $record,
                            (int) $data['home_score'],
                            (int) $data['away_score'],
                            $admin,
                            $penaltyWinnerId,
                        );
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
