<?php

namespace Database\Seeders;

use App\Models\FootballMatch;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentGroup;
use App\Models\TournamentPhase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WorldCup2026MatchesSeeder extends Seeder
{
    private const GROUP_FIXTURES = [
        ['A', '2026-06-11 15:00:00', 'México', 'Sudáfrica'],
        ['A', '2026-06-11 22:00:00', 'Corea del Sur', 'Chequia'],
        ['B', '2026-06-12 15:00:00', 'Canadá', 'Bosnia y Herzegovina'],
        ['D', '2026-06-12 21:00:00', 'Estados Unidos', 'Paraguay'],
        ['B', '2026-06-13 15:00:00', 'Catar', 'Suiza'],
        ['C', '2026-06-13 18:00:00', 'Brasil', 'Marruecos'],
        ['C', '2026-06-13 21:00:00', 'Haití', 'Escocia'],
        ['D', '2026-06-13 21:00:00', 'Australia', 'Turquía'],
        ['E', '2026-06-14 13:00:00', 'Alemania', 'Curazao'],
        ['F', '2026-06-14 16:00:00', 'Países Bajos', 'Japón'],
        ['E', '2026-06-14 19:00:00', 'Costa de Marfil', 'Ecuador'],
        ['F', '2026-06-14 22:00:00', 'Suecia', 'Túnez'],
        ['H', '2026-06-15 12:00:00', 'España', 'Cabo Verde'],
        ['G', '2026-06-15 15:00:00', 'Bélgica', 'Egipto'],
        ['H', '2026-06-15 18:00:00', 'Arabia Saudí', 'Uruguay'],
        ['G', '2026-06-15 21:00:00', 'Irán', 'Nueva Zelanda'],
        ['I', '2026-06-16 15:00:00', 'Francia', 'Senegal'],
        ['I', '2026-06-16 18:00:00', 'Irak', 'Noruega'],
        ['J', '2026-06-16 21:00:00', 'Argentina', 'Argelia'],
        ['J', '2026-06-16 23:00:00', 'Austria', 'Jordania'],
        ['K', '2026-06-17 13:00:00', 'Portugal', 'RD Congo'],
        ['L', '2026-06-17 16:00:00', 'Inglaterra', 'Croacia'],
        ['L', '2026-06-17 19:00:00', 'Ghana', 'Panamá'],
        ['K', '2026-06-17 22:00:00', 'Uzbekistán', 'Colombia'],
        ['A', '2026-06-18 12:00:00', 'Chequia', 'Sudáfrica'],
        ['B', '2026-06-18 15:00:00', 'Suiza', 'Bosnia y Herzegovina'],
        ['B', '2026-06-18 18:00:00', 'Canadá', 'Catar'],
        ['A', '2026-06-18 21:00:00', 'México', 'Corea del Sur'],
        ['D', '2026-06-19 15:00:00', 'Estados Unidos', 'Australia'],
        ['C', '2026-06-19 18:00:00', 'Escocia', 'Marruecos'],
        ['C', '2026-06-19 21:00:00', 'Brasil', 'Haití'],
        ['D', '2026-06-19 23:00:00', 'Turquía', 'Paraguay'],
        ['F', '2026-06-20 13:00:00', 'Países Bajos', 'Suecia'],
        ['E', '2026-06-20 16:00:00', 'Alemania', 'Costa de Marfil'],
        ['E', '2026-06-20 20:00:00', 'Ecuador', 'Curazao'],
        ['F', '2026-06-20 23:00:00', 'Túnez', 'Japón'],
        ['H', '2026-06-21 12:00:00', 'España', 'Arabia Saudí'],
        ['G', '2026-06-21 15:00:00', 'Bélgica', 'Irán'],
        ['H', '2026-06-21 18:00:00', 'Uruguay', 'Cabo Verde'],
        ['G', '2026-06-21 21:00:00', 'Nueva Zelanda', 'Egipto'],
        ['J', '2026-06-22 13:00:00', 'Argentina', 'Austria'],
        ['I', '2026-06-22 17:00:00', 'Francia', 'Irak'],
        ['I', '2026-06-22 20:00:00', 'Noruega', 'Senegal'],
        ['J', '2026-06-22 23:00:00', 'Jordania', 'Argelia'],
        ['K', '2026-06-23 13:00:00', 'Portugal', 'Uzbekistán'],
        ['L', '2026-06-23 16:00:00', 'Inglaterra', 'Ghana'],
        ['L', '2026-06-23 19:00:00', 'Panamá', 'Croacia'],
        ['K', '2026-06-23 22:00:00', 'Colombia', 'RD Congo'],
        ['B', '2026-06-24 15:00:00', 'Suiza', 'Canadá'],
        ['B', '2026-06-24 15:00:00', 'Bosnia y Herzegovina', 'Catar'],
        ['C', '2026-06-24 18:00:00', 'Escocia', 'Brasil'],
        ['C', '2026-06-24 18:00:00', 'Marruecos', 'Haití'],
        ['A', '2026-06-24 21:00:00', 'Chequia', 'México'],
        ['A', '2026-06-24 21:00:00', 'Sudáfrica', 'Corea del Sur'],
        ['E', '2026-06-25 16:00:00', 'Ecuador', 'Alemania'],
        ['E', '2026-06-25 16:00:00', 'Curazao', 'Costa de Marfil'],
        ['F', '2026-06-25 19:00:00', 'Japón', 'Suecia'],
        ['F', '2026-06-25 19:00:00', 'Túnez', 'Países Bajos'],
        ['D', '2026-06-25 22:00:00', 'Turquía', 'Estados Unidos'],
        ['D', '2026-06-25 22:00:00', 'Paraguay', 'Australia'],
        ['I', '2026-06-26 15:00:00', 'Noruega', 'Francia'],
        ['I', '2026-06-26 15:00:00', 'Senegal', 'Irak'],
        ['H', '2026-06-26 20:00:00', 'Cabo Verde', 'Arabia Saudí'],
        ['H', '2026-06-26 20:00:00', 'Uruguay', 'España'],
        ['G', '2026-06-26 23:00:00', 'Egipto', 'Irán'],
        ['G', '2026-06-26 23:00:00', 'Nueva Zelanda', 'Bélgica'],
        ['L', '2026-06-27 17:00:00', 'Panamá', 'Inglaterra'],
        ['L', '2026-06-27 17:00:00', 'Croacia', 'Ghana'],
        ['K', '2026-06-27 19:30:00', 'Colombia', 'Portugal'],
        ['K', '2026-06-27 19:30:00', 'RD Congo', 'Uzbekistán'],
        ['J', '2026-06-27 22:00:00', 'Argelia', 'Austria'],
        ['J', '2026-06-27 22:00:00', 'Jordania', 'Argentina'],
    ];

    private const KNOCKOUT_FIXTURES = [
        ['round_32', '2026-06-28 15:00:00', 'Segundo Grupo A', 'Segundo Grupo B'],
        ['round_32', '2026-06-29 13:00:00', 'Ganador Grupo C', 'Segundo Grupo F'],
        ['round_32', '2026-06-29 16:30:00', 'Ganador Grupo E', 'Mejor tercero A/B/C/D/F'],
        ['round_32', '2026-06-29 21:00:00', 'Ganador Grupo F', 'Segundo Grupo C'],
        ['round_32', '2026-06-30 13:00:00', 'Segundo Grupo E', 'Segundo Grupo I'],
        ['round_32', '2026-06-30 17:00:00', 'Ganador Grupo I', 'Mejor tercero C/D/F/G/H'],
        ['round_32', '2026-06-30 21:00:00', 'Ganador Grupo A', 'Mejor tercero C/E/F/H/I'],
        ['round_32', '2026-07-01 12:00:00', 'Ganador Grupo L', 'Mejor tercero E/H/I/J/K'],
        ['round_32', '2026-07-01 16:00:00', 'Ganador Grupo G', 'Mejor tercero A/E/H/I/J'],
        ['round_32', '2026-07-01 20:00:00', 'Ganador Grupo D', 'Mejor tercero B/E/F/I/J'],
        ['round_32', '2026-07-02 15:00:00', 'Ganador Grupo H', 'Segundo Grupo J'],
        ['round_32', '2026-07-02 19:00:00', 'Segundo Grupo K', 'Segundo Grupo L'],
        ['round_32', '2026-07-02 23:00:00', 'Ganador Grupo B', 'Mejor tercero E/F/G/I/J'],
        ['round_32', '2026-07-03 14:00:00', 'Segundo Grupo D', 'Segundo Grupo G'],
        ['round_32', '2026-07-03 18:00:00', 'Ganador Grupo J', 'Segundo Grupo H'],
        ['round_32', '2026-07-03 21:30:00', 'Ganador Grupo K', 'Mejor tercero D/E/I/J/L'],
        ['round_16', '2026-07-04 13:00:00', 'Ganador Partido 73', 'Ganador Partido 75'],
        ['round_16', '2026-07-04 17:00:00', 'Ganador Partido 74', 'Ganador Partido 77'],
        ['round_16', '2026-07-05 16:00:00', 'Ganador Partido 76', 'Ganador Partido 78'],
        ['round_16', '2026-07-05 20:00:00', 'Ganador Partido 79', 'Ganador Partido 80'],
        ['round_16', '2026-07-06 15:00:00', 'Ganador Partido 83', 'Ganador Partido 84'],
        ['round_16', '2026-07-06 20:00:00', 'Ganador Partido 81', 'Ganador Partido 82'],
        ['round_16', '2026-07-07 12:00:00', 'Ganador Partido 86', 'Ganador Partido 88'],
        ['round_16', '2026-07-07 16:00:00', 'Ganador Partido 85', 'Ganador Partido 87'],
        ['quarter_final', '2026-07-09 16:00:00', 'Ganador Partido 89', 'Ganador Partido 90'],
        ['quarter_final', '2026-07-10 15:00:00', 'Ganador Partido 93', 'Ganador Partido 94'],
        ['quarter_final', '2026-07-11 17:00:00', 'Ganador Partido 91', 'Ganador Partido 92'],
        ['quarter_final', '2026-07-11 21:00:00', 'Ganador Partido 95', 'Ganador Partido 96'],
        ['semi_final', '2026-07-14 15:00:00', 'Ganador Partido 97', 'Ganador Partido 98'],
        ['semi_final', '2026-07-15 15:00:00', 'Ganador Partido 99', 'Ganador Partido 100'],
        ['third_place', '2026-07-18 17:00:00', 'Perdedor Partido 101', 'Perdedor Partido 102'],
        ['final', '2026-07-19 15:00:00', 'Ganador Partido 101', 'Ganador Partido 102'],
    ];

    public function run(): void
    {
        $tournament = Tournament::where('slug', 'mundial-demo-2026')->firstOrFail();
        $groupStage = TournamentPhase::where('tournament_id', $tournament->id)->where('type', 'group_stage')->firstOrFail();

        foreach (self::GROUP_FIXTURES as $index => [$groupLetter, $startsAt, $home, $away]) {
            $this->createMatch($tournament, $groupStage, $startsAt, $home, $away, $groupLetter, $index + 1);
        }

        $phaseMap = [
            'round_32' => ['Dieciseisavos de final', 2],
            'round_16' => ['Octavos de final', 3],
            'quarter_final' => ['Cuartos de final', 4],
            'semi_final' => ['Semifinal', 5],
            'third_place' => ['Tercer puesto', 6],
            'final' => ['Final', 7],
        ];

        foreach (self::KNOCKOUT_FIXTURES as $index => [$type, $startsAt, $home, $away]) {
            [$name, $order] = $phaseMap[$type];
            $phase = TournamentPhase::firstOrCreate(
                ['tournament_id' => $tournament->id, 'name' => $name],
                ['type' => $type === 'final' ? 'final' : 'knockout', 'order' => $order, 'is_active' => true]
            );

            $this->createMatch($tournament, $phase, $startsAt, $home, $away, null, $index + 73, false);
        }
    }

    private function createMatch(Tournament $tournament, TournamentPhase $phase, string $startsAt, string $home, string $away, ?string $groupLetter, int $matchNumber, bool $activeTeams = true): void
    {
        $homeTeam = $activeTeams ? Team::where('name', $home)->firstOrFail() : $this->placeholderTeam($home);
        $awayTeam = $activeTeams ? Team::where('name', $away)->firstOrFail() : $this->placeholderTeam($away);
        $group = $groupLetter ? TournamentGroup::where('phase_id', $phase->id)->where('name', 'Grupo '.$groupLetter)->first() : null;

        FootballMatch::updateOrCreate(
            [
                'tournament_id' => $tournament->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'starts_at' => $startsAt,
            ],
            [
                'phase_id' => $phase->id,
                'group_id' => $group?->id,
                'prediction_closes_at' => date('Y-m-d H:i:s', strtotime($startsAt.' -1 hour')),
                'status' => 'scheduled',
            ]
        );
    }

    private function placeholderTeam(string $name): Team
    {
        return Team::firstOrCreate(
            ['slug' => 'slot-'.Str::slug($name)],
            [
                'name' => $name,
                'fifa_code' => 'TBD',
                'country' => 'Por definir',
                'description' => 'Placeholder de cruce de eliminatoria del Mundial 2026.',
                'is_active' => false,
            ]
        );
    }
}
