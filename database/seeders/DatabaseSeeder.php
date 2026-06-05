<?php

namespace Database\Seeders;

use App\Models\FootballMatch;
use App\Models\Prediction;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentGroup;
use App\Models\TournamentParticipant;
use App\Models\TournamentPhase;
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
                'name' => 'Super Admin',
                'phone' => '999999999',
                'email' => 'admin@example.com',
                'phone_verified_at' => now(),
                'is_active' => true,
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole($superAdminRole);

        $users = collect([
            ['name' => 'Usuario Demo Uno', 'phone' => '987654321', 'email' => 'demo1@example.com'],
            ['name' => 'Usuario Demo Dos', 'phone' => '987654322', 'email' => 'demo2@example.com'],
            ['name' => 'Usuario Demo Tres', 'phone' => '987654323', 'email' => 'demo3@example.com'],
        ])->map(function (array $data) {
            $user = User::updateOrCreate(
                ['phone_normalized' => $data['phone']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'email' => $data['email'],
                    'phone_verified_at' => now(),
                    'is_active' => true,
                    'password' => Hash::make('password'),
                ]
            );
            $user->assignRole('user');

            return $user;
        });

        $this->call(WorldCup2026TeamsSeeder::class);

        $tournament = Tournament::updateOrCreate(
            ['slug' => 'mundial-demo-2026'],
            [
                'name' => 'Mundial 2026',
                'description' => 'Polla oficial de prueba con selecciones, grupos y calendario del Mundial 2026.',
                'starts_at' => '2026-06-11 15:00:00',
                'ends_at' => '2026-07-19 15:00:00',
                'status' => 'open',
                'entry_fee' => 20,
                'currency' => 'PEN',
                'payment_whatsapp_number' => '51999999999',
                'exact_score_points' => 5,
                'correct_result_points' => 3,
                'wrong_prediction_points' => 0,
                'is_active' => true,
            ]
        );

        $phase = TournamentPhase::updateOrCreate(
            ['tournament_id' => $tournament->id, 'name' => 'Fase de grupos'],
            ['type' => 'group_stage', 'order' => 1, 'is_active' => true]
        );

        foreach (collect(WorldCup2026TeamsSeeder::TEAMS)->groupBy('group') as $groupName => $groupTeams) {
            $group = TournamentGroup::updateOrCreate(
                ['phase_id' => $phase->id, 'name' => 'Grupo '.$groupName],
                ['tournament_id' => $tournament->id, 'order' => ord($groupName) - 64]
            );
            $group->teams()->sync(Team::whereIn('name', $groupTeams->pluck('name'))->pluck('id')->all());
        }

        $this->call(WorldCup2026MatchesSeeder::class);

        $matchOne = FootballMatch::query()->where('tournament_id', $tournament->id)->orderBy('starts_at')->first();
        $matchTwo = FootballMatch::query()->where('tournament_id', $tournament->id)->orderBy('starts_at')->skip(1)->first();

        foreach ($users as $user) {
            TournamentParticipant::updateOrCreate(
                ['tournament_id' => $tournament->id, 'user_id' => $user->id],
                ['status' => 'approved', 'payment_status' => 'paid', 'paid_at' => now(), 'approved_at' => now(), 'approved_by' => $admin->id]
            );
        }

        $scores = [[2, 1], [1, 1], [0, 2]];
        foreach ($users->values() as $index => $user) {
            Prediction::updateOrCreate(
                ['match_id' => $matchOne->id, 'user_id' => $user->id],
                [
                    'tournament_id' => $tournament->id,
                    'predicted_home_score' => $scores[$index][0],
                    'predicted_away_score' => $scores[$index][1],
                ]
            );

            Prediction::updateOrCreate(
                ['match_id' => $matchTwo->id, 'user_id' => $user->id],
                [
                    'tournament_id' => $tournament->id,
                    'predicted_home_score' => 1,
                    'predicted_away_score' => 0,
                ]
            );
        }
        app(RankingService::class)->recalculateTournamentRanking($tournament);
    }
}
