<?php

namespace App\Filament\Resources\TournamentRankings\Pages;

use App\Filament\Resources\TournamentRankings\TournamentRankingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTournamentRanking extends EditRecord
{
    protected static string $resource = TournamentRankingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
