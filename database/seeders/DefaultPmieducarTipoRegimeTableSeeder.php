<?php

namespace Database\Seeders;

use App\Models\LegacyRegimeType;
use App\Models\LegacyUser;
use Illuminate\Database\Seeder;

class DefaultPmieducarTipoRegimeTableSeeder extends Seeder
{
    public function run()
    {
        $user = LegacyUser::query()
            ->orderBy('cod_usuario')
            ->first();

        $types = [
            'Seriado',
            'Etapas',
            'Modular',
            'Cíclico',
        ];

        foreach ($types as $type) {
            LegacyRegimeType::updateOrCreate([
                'nm_tipo' => $type,
            ], [
                'ativo' => 1,
                'ref_usuario_cad' => $user?->getKey(),
                'ref_cod_instituicao' => 1,
            ]);
        }
    }
}
