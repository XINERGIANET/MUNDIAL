<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WorldCup2026TeamsSeeder extends Seeder
{
    public const TEAMS = [
        ['group' => 'A', 'name' => 'Mexico', 'country' => 'Mexico', 'fifa_code' => 'MEX', 'flag' => 'mx'],
        ['group' => 'A', 'name' => 'South Africa', 'country' => 'South Africa', 'fifa_code' => 'RSA', 'flag' => 'za'],
        ['group' => 'A', 'name' => 'Korea Republic', 'country' => 'South Korea', 'fifa_code' => 'KOR', 'flag' => 'kr'],
        ['group' => 'A', 'name' => 'Czechia', 'country' => 'Czechia', 'fifa_code' => 'CZE', 'flag' => 'cz'],
        ['group' => 'B', 'name' => 'Canada', 'country' => 'Canada', 'fifa_code' => 'CAN', 'flag' => 'ca'],
        ['group' => 'B', 'name' => 'Bosnia and Herzegovina', 'country' => 'Bosnia and Herzegovina', 'fifa_code' => 'BIH', 'flag' => 'ba'],
        ['group' => 'B', 'name' => 'Qatar', 'country' => 'Qatar', 'fifa_code' => 'QAT', 'flag' => 'qa'],
        ['group' => 'B', 'name' => 'Switzerland', 'country' => 'Switzerland', 'fifa_code' => 'SUI', 'flag' => 'ch'],
        ['group' => 'C', 'name' => 'Brazil', 'country' => 'Brazil', 'fifa_code' => 'BRA', 'flag' => 'br'],
        ['group' => 'C', 'name' => 'Morocco', 'country' => 'Morocco', 'fifa_code' => 'MAR', 'flag' => 'ma'],
        ['group' => 'C', 'name' => 'Haiti', 'country' => 'Haiti', 'fifa_code' => 'HAI', 'flag' => 'ht'],
        ['group' => 'C', 'name' => 'Scotland', 'country' => 'Scotland', 'fifa_code' => 'SCO', 'flag' => 'gb-sct'],
        ['group' => 'D', 'name' => 'USA', 'country' => 'United States', 'fifa_code' => 'USA', 'flag' => 'us'],
        ['group' => 'D', 'name' => 'Paraguay', 'country' => 'Paraguay', 'fifa_code' => 'PAR', 'flag' => 'py'],
        ['group' => 'D', 'name' => 'Australia', 'country' => 'Australia', 'fifa_code' => 'AUS', 'flag' => 'au'],
        ['group' => 'D', 'name' => 'Türkiye', 'country' => 'Türkiye', 'fifa_code' => 'TUR', 'flag' => 'tr'],
        ['group' => 'E', 'name' => 'Germany', 'country' => 'Germany', 'fifa_code' => 'GER', 'flag' => 'de'],
        ['group' => 'E', 'name' => 'Curaçao', 'country' => 'Curaçao', 'fifa_code' => 'CUW', 'flag' => 'cw'],
        ['group' => 'E', 'name' => "Côte d'Ivoire", 'country' => "Côte d'Ivoire", 'fifa_code' => 'CIV', 'flag' => 'ci'],
        ['group' => 'E', 'name' => 'Ecuador', 'country' => 'Ecuador', 'fifa_code' => 'ECU', 'flag' => 'ec'],
        ['group' => 'F', 'name' => 'Netherlands', 'country' => 'Netherlands', 'fifa_code' => 'NED', 'flag' => 'nl'],
        ['group' => 'F', 'name' => 'Japan', 'country' => 'Japan', 'fifa_code' => 'JPN', 'flag' => 'jp'],
        ['group' => 'F', 'name' => 'Sweden', 'country' => 'Sweden', 'fifa_code' => 'SWE', 'flag' => 'se'],
        ['group' => 'F', 'name' => 'Tunisia', 'country' => 'Tunisia', 'fifa_code' => 'TUN', 'flag' => 'tn'],
        ['group' => 'G', 'name' => 'Belgium', 'country' => 'Belgium', 'fifa_code' => 'BEL', 'flag' => 'be'],
        ['group' => 'G', 'name' => 'Egypt', 'country' => 'Egypt', 'fifa_code' => 'EGY', 'flag' => 'eg'],
        ['group' => 'G', 'name' => 'IR Iran', 'country' => 'Iran', 'fifa_code' => 'IRN', 'flag' => 'ir'],
        ['group' => 'G', 'name' => 'New Zealand', 'country' => 'New Zealand', 'fifa_code' => 'NZL', 'flag' => 'nz'],
        ['group' => 'H', 'name' => 'Spain', 'country' => 'Spain', 'fifa_code' => 'ESP', 'flag' => 'es'],
        ['group' => 'H', 'name' => 'Cabo Verde', 'country' => 'Cabo Verde', 'fifa_code' => 'CPV', 'flag' => 'cv'],
        ['group' => 'H', 'name' => 'Saudi Arabia', 'country' => 'Saudi Arabia', 'fifa_code' => 'KSA', 'flag' => 'sa'],
        ['group' => 'H', 'name' => 'Uruguay', 'country' => 'Uruguay', 'fifa_code' => 'URU', 'flag' => 'uy'],
        ['group' => 'I', 'name' => 'France', 'country' => 'France', 'fifa_code' => 'FRA', 'flag' => 'fr'],
        ['group' => 'I', 'name' => 'Senegal', 'country' => 'Senegal', 'fifa_code' => 'SEN', 'flag' => 'sn'],
        ['group' => 'I', 'name' => 'Iraq', 'country' => 'Iraq', 'fifa_code' => 'IRQ', 'flag' => 'iq'],
        ['group' => 'I', 'name' => 'Norway', 'country' => 'Norway', 'fifa_code' => 'NOR', 'flag' => 'no'],
        ['group' => 'J', 'name' => 'Argentina', 'country' => 'Argentina', 'fifa_code' => 'ARG', 'flag' => 'ar'],
        ['group' => 'J', 'name' => 'Algeria', 'country' => 'Algeria', 'fifa_code' => 'ALG', 'flag' => 'dz'],
        ['group' => 'J', 'name' => 'Austria', 'country' => 'Austria', 'fifa_code' => 'AUT', 'flag' => 'at'],
        ['group' => 'J', 'name' => 'Jordan', 'country' => 'Jordan', 'fifa_code' => 'JOR', 'flag' => 'jo'],
        ['group' => 'K', 'name' => 'Portugal', 'country' => 'Portugal', 'fifa_code' => 'POR', 'flag' => 'pt'],
        ['group' => 'K', 'name' => 'Congo DR', 'country' => 'DR Congo', 'fifa_code' => 'COD', 'flag' => 'cd'],
        ['group' => 'K', 'name' => 'Uzbekistan', 'country' => 'Uzbekistan', 'fifa_code' => 'UZB', 'flag' => 'uz'],
        ['group' => 'K', 'name' => 'Colombia', 'country' => 'Colombia', 'fifa_code' => 'COL', 'flag' => 'co'],
        ['group' => 'L', 'name' => 'England', 'country' => 'England', 'fifa_code' => 'ENG', 'flag' => 'gb-eng'],
        ['group' => 'L', 'name' => 'Croatia', 'country' => 'Croatia', 'fifa_code' => 'CRO', 'flag' => 'hr'],
        ['group' => 'L', 'name' => 'Ghana', 'country' => 'Ghana', 'fifa_code' => 'GHA', 'flag' => 'gh'],
        ['group' => 'L', 'name' => 'Panama', 'country' => 'Panama', 'fifa_code' => 'PAN', 'flag' => 'pa'],
    ];

    public function run(): void
    {
        foreach (self::TEAMS as $team) {
            Team::updateOrCreate(
                ['slug' => Str::slug($team['name'])],
                [
                    'name' => $team['name'],
                    'logo_path' => "https://flagcdn.com/w160/{$team['flag']}.png",
                    'fifa_code' => $team['fifa_code'],
                    'country' => $team['country'],
                    'description' => "Seleccion clasificada al Mundial 2026 - Grupo {$team['group']}.",
                    'is_active' => true,
                ]
            );
        }
    }
}
