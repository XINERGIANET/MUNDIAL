<?php

namespace App\Filament\Resources\Tournaments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class TournamentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Torneo')->searchable()->sortable(),
                TextColumn::make('status')->label('Estado')->badge()->sortable(),
                TextColumn::make('starts_at')->label('Inicio')->dateTime()->sortable(),
                TextColumn::make('entry_fee')->label('Inscripcion')->money('PEN')->sortable(),
                IconColumn::make('is_active')->label('Activo')->boolean(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
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
