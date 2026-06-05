<?php

namespace App\Filament\Resources\TournamentParticipants;

use App\Filament\Resources\TournamentParticipants\Pages\CreateTournamentParticipant;
use App\Filament\Resources\TournamentParticipants\Pages\EditTournamentParticipant;
use App\Filament\Resources\TournamentParticipants\Pages\ListTournamentParticipants;
use App\Filament\Resources\TournamentParticipants\Schemas\TournamentParticipantForm;
use App\Filament\Resources\TournamentParticipants\Tables\TournamentParticipantsTable;
use App\Models\TournamentParticipant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TournamentParticipantResource extends Resource
{
    protected static ?string $model = TournamentParticipant::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return TournamentParticipantForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TournamentParticipantsTable::configure($table);
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
            'index' => ListTournamentParticipants::route('/'),
            'create' => CreateTournamentParticipant::route('/create'),
            'edit' => EditTournamentParticipant::route('/{record}/edit'),
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
