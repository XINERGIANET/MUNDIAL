<?php

namespace App\Filament\Resources\TournamentGroups\Pages;

use App\Filament\Resources\TournamentGroups\TournamentGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditTournamentGroup extends EditRecord
{
    protected static string $resource = TournamentGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
