<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('user.name')->searchable(),
                TextColumn::make('action')->searchable()->badge(),
                TextColumn::make('entity_type')->searchable(),
                TextColumn::make('entity_id'),
                TextColumn::make('ip_address'),
            ])
            ->filters([])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
