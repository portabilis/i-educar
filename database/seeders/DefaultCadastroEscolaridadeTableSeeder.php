<?php

namespace Database\Seeders;

use App\Models\LegacySchoolingDegree;
use iEducar\Modules\Educacenso\Model\Escolaridade;
use Illuminate\Database\Seeder;

class DefaultCadastroEscolaridadeTableSeeder extends Seeder
{
    public function run()
    {
        $schoolings = [
            Escolaridade::ENSINO_MEDIO => 'Ensino Médio',
            Escolaridade::ENSINO_FUNDAMENTAL => 'Ensino Fundamental Completo',
            Escolaridade::NAO_CONCLUIU_ENSINO_FUNDAMENTAL => 'Ensino Fundamental Incompleto',
            Escolaridade::EDUCACAO_SUPERIOR => 'Superior Completo',
        ];

        foreach ($schoolings as $id => $name) {
            LegacySchoolingDegree::updateOrCreate([
                'escolaridade' => $id,
            ], [
                'descricao' => $name,
            ]);
        }
    }
}
