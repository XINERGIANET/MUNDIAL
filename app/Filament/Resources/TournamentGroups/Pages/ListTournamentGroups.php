<?php

namespace App\Filament\Resources\TournamentGroups\Pages;

use App\Filament\Resources\TournamentGroups\TournamentGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTournamentGroups extends ListRecords
{
    protected static string $resource = TournamentGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
