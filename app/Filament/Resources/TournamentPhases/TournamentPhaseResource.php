<?php

namespace App\Filament\Resources\TournamentPhases;

use App\Filament\Resources\TournamentPhases\Pages\CreateTournamentPhase;
use App\Filament\Resources\TournamentPhases\Pages\EditTournamentPhase;
use App\Filament\Resources\TournamentPhases\Pages\ListTournamentPhases;
use App\Filament\Resources\TournamentPhases\Schemas\TournamentPhaseForm;
use App\Filament\Resources\TournamentPhases\Tables\TournamentPhasesTable;
use App\Models\TournamentPhase;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TournamentPhaseResource extends Resource
{
    protected static ?string $model = TournamentPhase::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static ?string $modelLabel = 'fase';

    protected static ?string $pluralModelLabel = 'fases';

    protected static ?string $navigationLabel = 'Fases';

    protected static \UnitEnum|string|null $navigationGroup = 'Torneos';

    public static function form(Schema $schema): Schema
    {
        return TournamentPhaseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TournamentPhasesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTournamentPhases::route('/'),
            'create' => CreateTournamentPhase::route('/create'),
            'edit' => EditTournamentPhase::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
