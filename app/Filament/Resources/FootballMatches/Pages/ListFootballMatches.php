<?php

namespace App\Filament\Resources\FootballMatches\Pages;

use App\Filament\Resources\FootballMatches\FootballMatchResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFootballMatches extends ListRecords
{
    protected static string $resource = FootballMatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('quickResults')
                ->label('Resultados rapidos')
                ->url(FootballMatchResource::getUrl('quick-results'))
                ->color('gray'),
            CreateAction::make(),
        ];
    }
}
