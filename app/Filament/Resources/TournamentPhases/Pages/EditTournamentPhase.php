<?php

namespace App\Filament\Resources\TournamentPhases\Pages;

use App\Filament\Resources\TournamentPhases\TournamentPhaseResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditTournamentPhase extends EditRecord
{
    protected static string $resource = TournamentPhaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
