<?php

namespace Database\Seeders;

use App\Models\LegacyEducationLevel;
use App\Models\LegacyUser;
use Illuminate\Database\Seeder;

class DefaultPmieducarNivelEnsinoTableSeeder extends Seeder
{
    public function run()
    {
        $user = LegacyUser::query()
            ->orderBy('cod_usuario')
            ->first();

        $levels = [
            'Ano',
        ];

        foreach ($levels as $level) {
            LegacyEducationLevel::updateOrCreate([
                'nm_nivel' => $level,
            ], [
                'ativo' => 1,
                'ref_usuario_cad' => $user?->getKey(),
                'ref_cod_instituicao' => 1,
            ]);
        }
    }
}
