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
use Illuminate\Support\Str;
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

        $teams = collect(['Mexico', 'Canada', 'Argentina', 'Brasil', 'Peru', 'Alemania', 'Francia', 'Espana'])
            ->mapWithKeys(fn (string $name) => [$name => Team::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'fifa_code' => strtoupper(substr($name, 0, 3)), 'country' => $name, 'is_active' => true]
            )]);

        $tournament = Tournament::updateOrCreate(
            ['slug' => 'mundial-demo-2026'],
            [
                'name' => 'Mundial Demo 2026',
                'description' => 'Torneo de prueba para la polla mundialista.',
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addMonth(),
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

        $group = TournamentGroup::updateOrCreate(
            ['phase_id' => $phase->id, 'name' => 'Grupo A'],
            ['tournament_id' => $tournament->id, 'order' => 1]
        );
        $group->teams()->sync($teams->take(4)->pluck('id')->all());

        $matchOne = FootballMatch::updateOrCreate(
            ['tournament_id' => $tournament->id, 'home_team_id' => $teams['Mexico']->id, 'away_team_id' => $teams['Canada']->id],
            [
                'phase_id' => $phase->id,
                'group_id' => $group->id,
                'starts_at' => now()->subHours(4),
                'prediction_closes_at' => now()->subHours(5),
                'status' => 'finished',
                'home_score' => 2,
                'away_score' => 2,
                'result_registered_by' => $admin->id,
                'result_registered_at' => now()->subHour(),
            ]
        );

        $matchTwo = FootballMatch::updateOrCreate(
            ['tournament_id' => $tournament->id, 'home_team_id' => $teams['Argentina']->id, 'away_team_id' => $teams['Brasil']->id],
            [
                'phase_id' => $phase->id,
                'group_id' => $group->id,
                'starts_at' => now()->addDay(),
                'prediction_closes_at' => now()->addHours(20),
                'status' => 'scheduled',
            ]
        );

        foreach ($users as $user) {
            TournamentParticipant::updateOrCreate(
                ['tournament_id' => $tournament->id, 'user_id' => $user->id],
                ['status' => 'approved', 'payment_status' => 'paid', 'paid_at' => now(), 'approved_at' => now(), 'approved_by' => $admin->id]
            );
        }

        $scores = [[2, 2], [1, 1], [2, 1]];
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

        app(RankingService::class)->recalculateMatchPredictions($matchOne->fresh());
        app(RankingService::class)->recalculateTournamentRanking($tournament);
    }
}
