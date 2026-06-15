<?php

namespace Database\Seeders;

use App\Models\LegacyTransferType;
use App\Models\LegacyUser;
use Illuminate\Database\Seeder;

class DefaultPmieducarTransferenciaTipoTableSeeder extends Seeder
{
    public function run()
    {
        $user = LegacyUser::query()
            ->orderBy('cod_usuario')
            ->first();

        $types = [
            'Mudança de Endereço',
            'Interesse da Família',
            'Adaptação Escolar',
            'Transporte Escolar',
            'Decisão Judicial / Conselho Tutelar',
            'Ocorrência Disciplinar Grave',
            'Outro(a)',
        ];

        foreach ($types as $type) {
            LegacyTransferType::updateOrCreate([
                'nm_tipo' => $type,
            ], [
                'ativo' => 1,
                'ref_usuario_cad' => $user?->getKey(),
                'ref_cod_instituicao' => 1,
            ]);
        }
    }
}
