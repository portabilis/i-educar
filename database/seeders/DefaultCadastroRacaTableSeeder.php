<?php

namespace Database\Seeders;

use App\Models\LegacyRace;
use App\Models\LegacyUser;
use Illuminate\Database\Seeder;

class DefaultCadastroRacaTableSeeder extends Seeder
{
    public function run()
    {
        $user = LegacyUser::query()
            ->orderBy('cod_usuario')
            ->first();

        $races = [
            0 => 'Não Declarada',
            1 => 'Branca',
            2 => 'Preta',
            3 => 'Parda',
            4 => 'Amarela',
            5 => 'Indígena',
        ];

        foreach ($races as $id => $name) {
            LegacyRace::updateOrCreate([
                'raca_educacenso' => $id,
            ], [
                'nm_raca' => $name,
                'idpes_cad' => $user?->getKey(),
            ]);
        }
    }
}
