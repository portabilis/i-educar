<?php

namespace Database\Seeders;

use App\Models\LegacyExemptionType;
use App\Models\LegacyUser;
use Illuminate\Database\Seeder;

class DefaultPmieducarTipoDispensaTableSeeder extends Seeder
{
    public function run()
    {
        $user = LegacyUser::query()
            ->orderBy('cod_usuario')
            ->first();

        $types = [
            'Prática de Educação Física (Lei Federal 10.793/2003)',
            'Escusa de Consciência (Lei 13.796/2019)',
            'Adaptação Curricular (PDI) - Educação Especial',
            'Outro(a)',
        ];

        foreach ($types as $type) {
            LegacyExemptionType::updateOrCreate([
                'nm_tipo' => $type,
            ], [
                'ativo' => 1,
                'ref_usuario_cad' => $user?->getKey(),
                'ref_cod_instituicao' => 1,
            ]);
        }
    }
}
