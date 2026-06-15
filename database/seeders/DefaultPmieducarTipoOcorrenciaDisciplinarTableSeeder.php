<?php

namespace Database\Seeders;

use App\Models\LegacyDisciplinaryOccurrenceType;
use App\Models\LegacyUser;
use Illuminate\Database\Seeder;

class DefaultPmieducarTipoOcorrenciaDisciplinarTableSeeder extends Seeder
{
    public function run()
    {
        $user = LegacyUser::query()
            ->orderBy('cod_usuario')
            ->first();

        $types = [
            'Indisciplina em Sala de Aula',
            'Desrespeito',
            'Agressão Física ou Verbal',
            'Dano ao Patrimônio Público',
            'Bullying ou Prática Discriminatória',
            'Uso Indevido de Celular',
            'Evasão de Sala de Aula (Saída sem Autorização)',
            'Outro(a)',
        ];

        foreach ($types as $type) {
            LegacyDisciplinaryOccurrenceType::updateOrCreate([
                'nm_tipo' => $type,
            ], [
                'ativo' => 1,
                'ref_usuario_cad' => $user?->getKey(),
                'ref_cod_instituicao' => 1,
            ]);
        }
    }
}
