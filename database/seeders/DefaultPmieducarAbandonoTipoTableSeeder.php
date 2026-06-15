<?php

namespace Database\Seeders;

use App\Models\LegacyAbandonmentType;
use Illuminate\Database\Seeder;

class DefaultPmieducarAbandonoTipoTableSeeder extends Seeder
{
    public function run()
    {
        $types = [
            'Evasão Escolar',
            'Vulnerabilidade Social Extrema',
            'Violência no Trajeto ou na Escola',
            'Conflito Familiar',
            'Gravidez na Adolescência',
            'Mudança de Residência sem Comunicação Prévia',
            'Inserção no Mercado de Trabalho',
            'Doença / Tratamento de Saúde',
            'Outro(a)',
        ];

        foreach ($types as $type) {
            LegacyAbandonmentType::updateOrCreate([
                'nome' => $type,
            ], [
                'ref_cod_instituicao' => 1,
                'ativo' => 1,
            ]);
        }
    }
}
