<?php

namespace App\Filament\Resources\TournamentRankings;

use App\Filament\Resources\TournamentRankings\Pages\CreateTournamentRanking;
use App\Filament\Resources\TournamentRankings\Pages\EditTournamentRanking;
use App\Filament\Resources\TournamentRankings\Pages\ListTournamentRankings;
use App\Filament\Resources\TournamentRankings\Schemas\TournamentRankingForm;
use App\Filament\Resources\TournamentRankings\Tables\TournamentRankingsTable;
use App\Models\TournamentRanking;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TournamentRankingResource extends Resource
{
    protected static ?string $model = TournamentRanking::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $modelLabel = 'ranking';

    protected static ?string $pluralModelLabel = 'ranking general';

    protected static ?string $navigationLabel = 'Ranking';

    protected static \UnitEnum|string|null $navigationGroup = 'Reportes';

    public static function form(Schema $schema): Schema
    {
        return TournamentRankingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TournamentRankingsTable::configure($table);
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
            'index' => ListTournamentRankings::route('/'),
            'create' => CreateTournamentRanking::route('/create'),
            'edit' => EditTournamentRanking::route('/{record}/edit'),
        ];
    }
}
