<?php

namespace App\Filament\Resources\TournamentPhases\Pages;

use App\Filament\Resources\TournamentPhases\TournamentPhaseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTournamentPhases extends ListRecords
{
    protected static string $resource = TournamentPhaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
