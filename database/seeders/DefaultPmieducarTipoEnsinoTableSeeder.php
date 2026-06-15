<?php

namespace Database\Seeders;

use App\Models\LegacyEducationType;
use App\Models\LegacyUser;
use Illuminate\Database\Seeder;

class DefaultPmieducarTipoEnsinoTableSeeder extends Seeder
{
    public function run()
    {
        $user = LegacyUser::query()
            ->orderBy('cod_usuario')
            ->first();

        $levels = [
            'AEE',
            'EJA',
            'Educação Infantil',
            'Ensino Fundamental',
            'Atividade Complementar',
        ];

        foreach ($levels as $level) {
            LegacyEducationType::updateOrCreate([
                'nm_tipo' => $level,
            ], [
                'ativo' => 1,
                'ref_usuario_cad' => $user?->getKey(),
                'ref_cod_instituicao' => 1,
            ]);
        }
    }
}
