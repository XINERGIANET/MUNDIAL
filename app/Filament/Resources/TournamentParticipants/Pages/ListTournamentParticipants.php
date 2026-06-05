<?php

namespace App\Filament\Resources\TournamentParticipants\Pages;

use App\Filament\Resources\TournamentParticipants\TournamentParticipantResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTournamentParticipants extends ListRecords
{
    protected static string $resource = TournamentParticipantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
