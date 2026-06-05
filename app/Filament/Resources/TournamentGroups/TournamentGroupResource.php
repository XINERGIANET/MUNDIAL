<?php

namespace App\Filament\Resources\TournamentGroups;

use App\Filament\Resources\TournamentGroups\Pages\CreateTournamentGroup;
use App\Filament\Resources\TournamentGroups\Pages\EditTournamentGroup;
use App\Filament\Resources\TournamentGroups\Pages\ListTournamentGroups;
use App\Filament\Resources\TournamentGroups\Schemas\TournamentGroupForm;
use App\Filament\Resources\TournamentGroups\Tables\TournamentGroupsTable;
use App\Models\TournamentGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TournamentGroupResource extends Resource
{
    protected static ?string $model = TournamentGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?string $modelLabel = 'grupo';

    protected static ?string $pluralModelLabel = 'grupos';

    protected static ?string $navigationLabel = 'Grupos';

    protected static \UnitEnum|string|null $navigationGroup = 'Torneos';

    public static function form(Schema $schema): Schema
    {
        return TournamentGroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TournamentGroupsTable::configure($table);
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
            'index' => ListTournamentGroups::route('/'),
            'create' => CreateTournamentGroup::route('/create'),
            'edit' => EditTournamentGroup::route('/{record}/edit'),
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
