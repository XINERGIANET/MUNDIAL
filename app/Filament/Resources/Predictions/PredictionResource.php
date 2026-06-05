<?php

namespace App\Filament\Resources\Predictions;

use App\Filament\Resources\Predictions\Pages\CreatePrediction;
use App\Filament\Resources\Predictions\Pages\EditPrediction;
use App\Filament\Resources\Predictions\Pages\ListPredictions;
use App\Filament\Resources\Predictions\Schemas\PredictionForm;
use App\Filament\Resources\Predictions\Tables\PredictionsTable;
use App\Models\Prediction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PredictionResource extends Resource
{
    protected static ?string $model = Prediction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'pronostico';

    protected static ?string $pluralModelLabel = 'pronosticos';

    protected static ?string $navigationLabel = 'Pronosticos';

    protected static \UnitEnum|string|null $navigationGroup = 'Operaciones';

    public static function form(Schema $schema): Schema
    {
        return PredictionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PredictionsTable::configure($table);
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
            'index' => ListPredictions::route('/'),
            'create' => CreatePrediction::route('/create'),
            'edit' => EditPrediction::route('/{record}/edit'),
        ];
    }
}
