<?php

namespace Database\Seeders;

use App\Models\LegacyStageType;
use App\Models\LegacyUser;
use Illuminate\Database\Seeder;

class DefaultPmieducarModuloTableSeeder extends Seeder
{
    public function run()
    {
        $user = LegacyUser::query()
            ->orderBy('cod_usuario')
            ->first();

        $modules = [
            1 => 'Ano',
            2 => 'Semestre',
            3 => 'Trimestre',
            4 => 'Bimestre',
        ];

        foreach ($modules as $stage => $name) {
            LegacyStageType::updateOrCreate([
                'num_etapas' => $stage,
                'nm_tipo' => $name,
            ], [
                'ativo' => 1,
                'ref_usuario_cad' => $user?->getKey(),
                'ref_cod_instituicao' => 1,
            ]);
        }
    }
}
