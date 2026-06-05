<?php

namespace App\Filament\Resources\TournamentRankings\Pages;

use App\Filament\Resources\TournamentRankings\TournamentRankingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTournamentRankings extends ListRecords
{
    protected static string $resource = TournamentRankingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
