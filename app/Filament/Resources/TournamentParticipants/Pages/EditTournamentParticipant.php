<?php

namespace App\Filament\Resources\TournamentParticipants\Pages;

use App\Filament\Resources\TournamentParticipants\TournamentParticipantResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditTournamentParticipant extends EditRecord
{
    protected static string $resource = TournamentParticipantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
