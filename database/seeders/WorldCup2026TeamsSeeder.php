<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WorldCup2026TeamsSeeder extends Seeder
{
    // Solo incluye los equipos confirmados para los Dieciseisavos de Final.
    // Se irán agregando el resto conforme se confirmen (grupos D-L terminan el 25-27 jun).
    public const TEAMS = [
        ['name' => 'México',         'country' => 'México',         'fifa_code' => 'MEX', 'flag' => 'mx'],
        ['name' => 'Sudáfrica',      'country' => 'Sudáfrica',      'fifa_code' => 'RSA', 'flag' => 'za'],
        ['name' => 'Canadá',         'country' => 'Canadá',         'fifa_code' => 'CAN', 'flag' => 'ca'],
        ['name' => 'Suiza',          'country' => 'Suiza',          'fifa_code' => 'SUI', 'flag' => 'ch'],
        ['name' => 'Brasil',         'country' => 'Brasil',         'fifa_code' => 'BRA', 'flag' => 'br'],
        ['name' => 'Marruecos',      'country' => 'Marruecos',      'fifa_code' => 'MAR', 'flag' => 'ma'],
        ['name' => 'Estados Unidos', 'country' => 'Estados Unidos', 'fifa_code' => 'USA', 'flag' => 'us'],
        ['name' => 'Alemania',       'country' => 'Alemania',       'fifa_code' => 'GER', 'flag' => 'de'],
        ['name' => 'Argentina',      'country' => 'Argentina',      'fifa_code' => 'ARG', 'flag' => 'ar'],
        ['name' => 'Francia',        'country' => 'Francia',        'fifa_code' => 'FRA', 'flag' => 'fr'],
        ['name' => 'Noruega',        'country' => 'Noruega',        'fifa_code' => 'NOR', 'flag' => 'no'],
        ['name' => 'Colombia',       'country' => 'Colombia',       'fifa_code' => 'COL', 'flag' => 'co'],
        ['name' => 'Costa de Marfil',      'country' => 'Costa de Marfil',      'fifa_code' => 'CIV', 'flag' => 'ci'],
        ['name' => 'Bosnia y Herzegovina', 'country' => 'Bosnia y Herzegovina', 'fifa_code' => 'BIH', 'flag' => 'ba'],
    ];

    public function run(): void
    {
        foreach (self::TEAMS as $team) {
            Team::updateOrCreate(
                ['slug' => Str::slug($team['name'])],
                [
                    'name'        => $team['name'],
                    'logo_path'   => "https://flagcdn.com/w160/{$team['flag']}.png",
                    'fifa_code'   => $team['fifa_code'],
                    'country'     => $team['country'],
                    'description' => 'Selección clasificada a los Dieciseisavos del Mundial 2026.',
                    'is_active'   => true,
                ]
            );
        }
    }
}
