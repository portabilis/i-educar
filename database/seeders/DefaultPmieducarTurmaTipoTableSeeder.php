<?php

namespace Database\Seeders;

use App\Models\LegacySchoolClassType;
use App\Models\LegacyUser;
use Illuminate\Database\Seeder;

class DefaultPmieducarTurmaTipoTableSeeder extends Seeder
{
    public function run()
    {
        $user = LegacyUser::query()
            ->orderBy('cod_usuario')
            ->first();

        $types = [
            'REG' => 'Regular',
            'ESP' => 'Especial',
        ];

        foreach ($types as $sg => $type) {
            LegacySchoolClassType::updateOrCreate([
                'sgl_tipo' => $sg,
            ], [
                'nm_tipo' => $type,
                'ativo' => 1,
                'ref_usuario_cad' => $user?->getKey(),
                'ref_cod_instituicao' => 1,
            ]);
        }
    }
}
