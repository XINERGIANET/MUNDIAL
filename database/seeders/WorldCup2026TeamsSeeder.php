<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WorldCup2026TeamsSeeder extends Seeder
{
    public const TEAMS = [
        ['group' => 'A', 'name' => 'México', 'country' => 'México', 'fifa_code' => 'MEX', 'flag' => 'mx'],
        ['group' => 'A', 'name' => 'Sudáfrica', 'country' => 'Sudáfrica', 'fifa_code' => 'RSA', 'flag' => 'za'],
        ['group' => 'A', 'name' => 'Corea del Sur', 'country' => 'Corea del Sur', 'fifa_code' => 'KOR', 'flag' => 'kr'],
        ['group' => 'A', 'name' => 'Chequia', 'country' => 'Chequia', 'fifa_code' => 'CZE', 'flag' => 'cz'],
        ['group' => 'B', 'name' => 'Canadá', 'country' => 'Canadá', 'fifa_code' => 'CAN', 'flag' => 'ca'],
        ['group' => 'B', 'name' => 'Bosnia y Herzegovina', 'country' => 'Bosnia y Herzegovina', 'fifa_code' => 'BIH', 'flag' => 'ba'],
        ['group' => 'B', 'name' => 'Catar', 'country' => 'Catar', 'fifa_code' => 'QAT', 'flag' => 'qa'],
        ['group' => 'B', 'name' => 'Suiza', 'country' => 'Suiza', 'fifa_code' => 'SUI', 'flag' => 'ch'],
        ['group' => 'C', 'name' => 'Brasil', 'country' => 'Brasil', 'fifa_code' => 'BRA', 'flag' => 'br'],
        ['group' => 'C', 'name' => 'Marruecos', 'country' => 'Marruecos', 'fifa_code' => 'MAR', 'flag' => 'ma'],
        ['group' => 'C', 'name' => 'Haití', 'country' => 'Haití', 'fifa_code' => 'HAI', 'flag' => 'ht'],
        ['group' => 'C', 'name' => 'Escocia', 'country' => 'Escocia', 'fifa_code' => 'SCO', 'flag' => 'gb-sct'],
        ['group' => 'D', 'name' => 'Estados Unidos', 'country' => 'Estados Unidos', 'fifa_code' => 'USA', 'flag' => 'us'],
        ['group' => 'D', 'name' => 'Paraguay', 'country' => 'Paraguay', 'fifa_code' => 'PAR', 'flag' => 'py'],
        ['group' => 'D', 'name' => 'Australia', 'country' => 'Australia', 'fifa_code' => 'AUS', 'flag' => 'au'],
        ['group' => 'D', 'name' => 'Turquía', 'country' => 'Turquía', 'fifa_code' => 'TUR', 'flag' => 'tr'],
        ['group' => 'E', 'name' => 'Alemania', 'country' => 'Alemania', 'fifa_code' => 'GER', 'flag' => 'de'],
        ['group' => 'E', 'name' => 'Curazao', 'country' => 'Curazao', 'fifa_code' => 'CUW', 'flag' => 'cw'],
        ['group' => 'E', 'name' => 'Costa de Marfil', 'country' => 'Costa de Marfil', 'fifa_code' => 'CIV', 'flag' => 'ci'],
        ['group' => 'E', 'name' => 'Ecuador', 'country' => 'Ecuador', 'fifa_code' => 'ECU', 'flag' => 'ec'],
        ['group' => 'F', 'name' => 'Países Bajos', 'country' => 'Países Bajos', 'fifa_code' => 'NED', 'flag' => 'nl'],
        ['group' => 'F', 'name' => 'Japón', 'country' => 'Japón', 'fifa_code' => 'JPN', 'flag' => 'jp'],
        ['group' => 'F', 'name' => 'Suecia', 'country' => 'Suecia', 'fifa_code' => 'SWE', 'flag' => 'se'],
        ['group' => 'F', 'name' => 'Túnez', 'country' => 'Túnez', 'fifa_code' => 'TUN', 'flag' => 'tn'],
        ['group' => 'G', 'name' => 'Bélgica', 'country' => 'Bélgica', 'fifa_code' => 'BEL', 'flag' => 'be'],
        ['group' => 'G', 'name' => 'Egipto', 'country' => 'Egipto', 'fifa_code' => 'EGY', 'flag' => 'eg'],
        ['group' => 'G', 'name' => 'Irán', 'country' => 'Irán', 'fifa_code' => 'IRN', 'flag' => 'ir'],
        ['group' => 'G', 'name' => 'Nueva Zelanda', 'country' => 'Nueva Zelanda', 'fifa_code' => 'NZL', 'flag' => 'nz'],
        ['group' => 'H', 'name' => 'España', 'country' => 'España', 'fifa_code' => 'ESP', 'flag' => 'es'],
        ['group' => 'H', 'name' => 'Cabo Verde', 'country' => 'Cabo Verde', 'fifa_code' => 'CPV', 'flag' => 'cv'],
        ['group' => 'H', 'name' => 'Arabia Saudí', 'country' => 'Arabia Saudí', 'fifa_code' => 'KSA', 'flag' => 'sa'],
        ['group' => 'H', 'name' => 'Uruguay', 'country' => 'Uruguay', 'fifa_code' => 'URU', 'flag' => 'uy'],
        ['group' => 'I', 'name' => 'Francia', 'country' => 'Francia', 'fifa_code' => 'FRA', 'flag' => 'fr'],
        ['group' => 'I', 'name' => 'Senegal', 'country' => 'Senegal', 'fifa_code' => 'SEN', 'flag' => 'sn'],
        ['group' => 'I', 'name' => 'Irak', 'country' => 'Irak', 'fifa_code' => 'IRQ', 'flag' => 'iq'],
        ['group' => 'I', 'name' => 'Noruega', 'country' => 'Noruega', 'fifa_code' => 'NOR', 'flag' => 'no'],
        ['group' => 'J', 'name' => 'Argentina', 'country' => 'Argentina', 'fifa_code' => 'ARG', 'flag' => 'ar'],
        ['group' => 'J', 'name' => 'Argelia', 'country' => 'Argelia', 'fifa_code' => 'ALG', 'flag' => 'dz'],
        ['group' => 'J', 'name' => 'Austria', 'country' => 'Austria', 'fifa_code' => 'AUT', 'flag' => 'at'],
        ['group' => 'J', 'name' => 'Jordania', 'country' => 'Jordania', 'fifa_code' => 'JOR', 'flag' => 'jo'],
        ['group' => 'K', 'name' => 'Portugal', 'country' => 'Portugal', 'fifa_code' => 'POR', 'flag' => 'pt'],
        ['group' => 'K', 'name' => 'RD Congo', 'country' => 'RD Congo', 'fifa_code' => 'COD', 'flag' => 'cd'],
        ['group' => 'K', 'name' => 'Uzbekistán', 'country' => 'Uzbekistán', 'fifa_code' => 'UZB', 'flag' => 'uz'],
        ['group' => 'K', 'name' => 'Colombia', 'country' => 'Colombia', 'fifa_code' => 'COL', 'flag' => 'co'],
        ['group' => 'L', 'name' => 'Inglaterra', 'country' => 'Inglaterra', 'fifa_code' => 'ENG', 'flag' => 'gb-eng'],
        ['group' => 'L', 'name' => 'Croacia', 'country' => 'Croacia', 'fifa_code' => 'CRO', 'flag' => 'hr'],
        ['group' => 'L', 'name' => 'Ghana', 'country' => 'Ghana', 'fifa_code' => 'GHA', 'flag' => 'gh'],
        ['group' => 'L', 'name' => 'Panamá', 'country' => 'Panamá', 'fifa_code' => 'PAN', 'flag' => 'pa'],
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
                    'description' => "Selección clasificada al Mundial 2026 - Grupo {$team['group']}.",
                    'is_active' => true,
                ]
            );
        }
    }
}
