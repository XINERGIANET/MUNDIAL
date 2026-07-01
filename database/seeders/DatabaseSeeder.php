<?php

namespace Database\Seeders;

use App\Models\Tournament;
use App\Models\User;
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
                'payment_yape_number'     => '944 031 514',
                'exact_score_points'      => 5,
                'correct_result_points'   => 3,
                'wrong_prediction_points' => 0,
                'is_active'               => true,
            ]
        );

        $this->call(WorldCup2026MatchesSeeder::class);
        $this->call(WorldCup2026RoundOf32Seeder::class);
    }
}
