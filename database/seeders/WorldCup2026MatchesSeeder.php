<?php

namespace Database\Seeders;

use App\Models\FootballMatch;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentPhase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WorldCup2026MatchesSeeder extends Seeder
{
    // La polla cubre desde Octavos de Final en adelante (15 partidos).
    // Horarios en UTC (hora Lima UTC-5 + 5h).
    // Los slots de octavos se actualizan conforme se confirmen los clasificados (3-4 jul).
    private const FIXTURES = [
        ['round_16',      '2026-07-04 13:00:00', 'Clasificado A1',  'Clasificado B2'],  // Vie 4/7 8:00 AM Lima
        ['round_16',      '2026-07-04 17:00:00', 'Clasificado C1',  'Clasificado D2'],  // Vie 4/7 12:00 PM Lima
        ['round_16',      '2026-07-05 13:00:00', 'Clasificado E1',  'Clasificado F2'],  // Sáb 5/7 8:00 AM Lima
        ['round_16',      '2026-07-05 17:00:00', 'Clasificado G1',  'Clasificado H2'],  // Sáb 5/7 12:00 PM Lima
        ['round_16',      '2026-07-06 13:00:00', 'Clasificado I1',  'Clasificado J2'],  // Dom 6/7 8:00 AM Lima
        ['round_16',      '2026-07-06 17:00:00', 'Clasificado K1',  'Clasificado L2'],  // Dom 6/7 12:00 PM Lima
        ['round_16',      '2026-07-07 13:00:00', 'Clasificado B1',  'Clasificado A2'],  // Lun 7/7 8:00 AM Lima
        ['round_16',      '2026-07-07 17:00:00', 'Clasificado D1',  'Clasificado C2'],  // Lun 7/7 12:00 PM Lima
        ['quarter_final', '2026-07-09 16:00:00', 'Ganador Octavo 1', 'Ganador Octavo 2'],
        ['quarter_final', '2026-07-10 15:00:00', 'Ganador Octavo 3', 'Ganador Octavo 4'],
        ['quarter_final', '2026-07-11 17:00:00', 'Ganador Octavo 5', 'Ganador Octavo 6'],
        ['quarter_final', '2026-07-11 21:00:00', 'Ganador Octavo 7', 'Ganador Octavo 8'],
        ['semi_final',    '2026-07-14 15:00:00', 'Ganador Cuarto 1', 'Ganador Cuarto 2'],
        ['semi_final',    '2026-07-15 15:00:00', 'Ganador Cuarto 3', 'Ganador Cuarto 4'],
        ['third_place',   '2026-07-18 17:00:00', 'Perdedor Semi 1',  'Perdedor Semi 2'],
        ['final',         '2026-07-19 15:00:00', 'Ganador Semi 1',   'Ganador Semi 2'],
    ];

    public function run(): void
    {
        $tournament = Tournament::where('slug', 'mundial-demo-2026')->firstOrFail();

        $phaseMap = [
            'round_16'      => ['Octavos de final', 1],
            'quarter_final' => ['Cuartos de final', 2],
            'semi_final'    => ['Semifinal',        3],
            'third_place'   => ['Tercer puesto',    4],
            'final'         => ['Final',            5],
        ];

        foreach (self::FIXTURES as [$type, $startsAt, $home, $away]) {
            [$name, $order] = $phaseMap[$type];
            $phase = TournamentPhase::updateOrCreate(
                ['tournament_id' => $tournament->id, 'name' => $name],
                ['type' => $type === 'final' ? 'final' : 'knockout', 'order' => $order, 'is_active' => true]
            );

            FootballMatch::updateOrCreate(
                [
                    'tournament_id' => $tournament->id,
                    'home_team_id'  => $this->placeholderTeam($home)->id,
                    'away_team_id'  => $this->placeholderTeam($away)->id,
                    'starts_at'     => $startsAt,
                ],
                [
                    'phase_id'             => $phase->id,
                    'group_id'             => null,
                    'prediction_closes_at' => date('Y-m-d H:i:s', strtotime($startsAt . ' -10 minutes')),
                    'status'               => 'scheduled',
                ]
            );
        }
    }

    private function placeholderTeam(string $name): Team
    {
        return Team::firstOrCreate(
            ['slug' => 'slot-' . Str::slug($name)],
            [
                'name'        => $name,
                'fifa_code'   => 'TBD',
                'country'     => 'Por confirmar',
                'description' => 'Clasificación pendiente de confirmarse.',
                'is_active'   => false,
            ]
        );
    }
}
