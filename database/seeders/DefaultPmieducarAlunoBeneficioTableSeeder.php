<?php

namespace Database\Seeders;

use App\Models\LegacyBenefit;
use App\Models\LegacyUser;
use Illuminate\Database\Seeder;

class DefaultPmieducarAlunoBeneficioTableSeeder extends Seeder
{
    public function run()
    {
        $user = LegacyUser::query()
            ->orderBy('cod_usuario')
            ->first();

        $benefits = [
            'Bolsa Família',
            'Auxílio Municipal',
            'Bolsa Estudantil',
            'Passe/ Vale Transporte',
            'Auxílio Uniforme Escolar',
            'Auxílio Material Escolar',
            'Outro(a)',
        ];

        foreach ($benefits as $benefit) {
            LegacyBenefit::updateOrCreate([
                'nm_beneficio' => $benefit,
            ], [
                'ref_usuario_cad' => $user?->getKey(),
            ]);
        }
    }
}
