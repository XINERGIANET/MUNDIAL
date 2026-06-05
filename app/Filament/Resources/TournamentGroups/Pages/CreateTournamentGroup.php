<?php

namespace App\Filament\Resources\TournamentGroups\Pages;

use App\Filament\Resources\TournamentGroups\TournamentGroupResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTournamentGroup extends CreateRecord
{
    protected static string $resource = TournamentGroupResource::class;
}
