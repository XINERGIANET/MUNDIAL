<?php

namespace Database\Seeders;

use App\Models\FootballMatch;
use App\Models\Prediction;
use App\Models\Tournament;
use App\Models\TournamentParticipant;
use App\Models\User;
use App\Services\RankingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        Role::firstOrCreate(['name' => 'tournament_admin']);
        Role::firstOrCreate(['name' => 'user']);

        $admin = User::updateOrCreate(
            ['phone_normalized' => '999999999'],
            [
                'name'              => 'Super Admin',
                'phone'             => '999999999',
                'email'             => 'admin@xpande.local',
                'phone_verified_at' => now(),
                'is_active'         => true,
                'password'          => Hash::make('password'),
            ]
        );
        $admin->assignRole($superAdminRole);

        $users = collect([
            ['name' => 'Usuario Demo Uno',  'phone' => '987654321', 'email' => 'demo1@example.com'],
            ['name' => 'Usuario Demo Dos',  'phone' => '987654322', 'email' => 'demo2@example.com'],
            ['name' => 'Usuario Demo Tres', 'phone' => '987654323', 'email' => 'demo3@example.com'],
        ])->map(function (array $data) {
            $user = User::updateOrCreate(
                ['phone_normalized' => $data['phone']],
                [
                    'name'              => $data['name'],
                    'phone'             => $data['phone'],
                    'email'             => $data['email'],
                    'phone_verified_at' => now(),
                    'is_active'         => true,
                    'password'          => Hash::make('password'),
                ]
            );
            $user->assignRole('user');

            return $user;
        });

        $this->call(WorldCup2026TeamsSeeder::class);

        $tournament = Tournament::updateOrCreate(
            ['slug' => 'mundial-demo-2026'],
            [
                'name'                    => 'Mundial 2026',
                'description'             => 'Polla oficial del Mundial 2026 — fase eliminatoria desde los Octavos de Final.',
                'starts_at'               => '2026-07-04 13:00:00',
                'ends_at'                 => '2026-07-19 15:00:00',
                'status'                  => 'open',
                'entry_fee'               => 20,
                'currency'                => 'PEN',
                'payment_whatsapp_number' => '51999999999',
                'payment_yape_number'     => '999 999 999',
                'exact_score_points'      => 5,
                'correct_result_points'   => 3,
                'wrong_prediction_points' => 0,
                'is_active'               => true,
            ]
        );

        $this->call(WorldCup2026MatchesSeeder::class);

        $matchOne = FootballMatch::query()->where('tournament_id', $tournament->id)->orderBy('starts_at')->first();
        $matchTwo = FootballMatch::query()->where('tournament_id', $tournament->id)->orderBy('starts_at')->skip(1)->first();

        // Crear una jugada por cada usuario demo
        $participants = $users->map(function ($user) use ($tournament, $admin) {
            return TournamentParticipant::create([
                'tournament_id'  => $tournament->id,
                'user_id'        => $user->id,
                'entry_name'     => $user->name . ' 01',
                'status'         => 'approved',
                'payment_status' => 'paid',
                'paid_at'        => now(),
                'approved_at'    => now(),
                'approved_by'    => $admin->id,
            ]);
        });

        // Segunda jugada para el primer usuario (demo de múltiples jugadas)
        $secondEntry = TournamentParticipant::create([
            'tournament_id'  => $tournament->id,
            'user_id'        => $users->first()->id,
            'entry_name'     => $users->first()->name . ' 02',
            'status'         => 'approved',
            'payment_status' => 'paid',
            'paid_at'        => now(),
            'approved_at'    => now(),
            'approved_by'    => $admin->id,
        ]);

        $scores = [[2, 1], [1, 1], [0, 2]];
        foreach ($participants->values() as $index => $participant) {
            Prediction::create([
                'match_id'             => $matchOne->id,
                'tournament_id'        => $tournament->id,
                'user_id'              => $participant->user_id,
                'participant_id'       => $participant->id,
                'predicted_home_score' => $scores[$index][0],
                'predicted_away_score' => $scores[$index][1],
            ]);

            Prediction::create([
                'match_id'             => $matchTwo->id,
                'tournament_id'        => $tournament->id,
                'user_id'              => $participant->user_id,
                'participant_id'       => $participant->id,
                'predicted_home_score' => 1,
                'predicted_away_score' => 0,
            ]);
        }

        // Pronósticos para la segunda jugada del primer usuario
        Prediction::create([
            'match_id'             => $matchOne->id,
            'tournament_id'        => $tournament->id,
            'user_id'              => $users->first()->id,
            'participant_id'       => $secondEntry->id,
            'predicted_home_score' => 0,
            'predicted_away_score' => 1,
        ]);

        app(RankingService::class)->recalculateTournamentRanking($tournament);
    }
}
