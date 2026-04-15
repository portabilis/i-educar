<?php

namespace Database\Seeders;

use App\Models\DeficiencyType;
use App\Models\LegacyDeficiency;
use iEducar\Modules\Educacenso\Model\Deficiencias;
use iEducar\Modules\Educacenso\Model\Transtornos;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DefaultCadastroDeficienciaTableSeeder extends Seeder
{
    public function run()
    {
        $deficiencies = [
            Deficiencias::CEGUEIRA => 'Cegueira',
            Deficiencias::ALTAS_HABILIDADES_SUPERDOTACAO => 'Altas Habilidades / Superdotação',
            Deficiencias::TRANSTORNO_ESPECTRO_AUTISTA => 'Transtorno do Espectro Autista',
            Deficiencias::BAIXA_VISAO => 'Baixa Visão',
            Deficiencias::DEFICIENCIA_AUDITIVA => 'Deficiência Auditiva',
            Deficiencias::DEFICIENCIA_FISICA => 'Deficiência Física',
            Deficiencias::DEFICIENCIA_INTELECTUAL => 'Deficiência Intelectual',
            Deficiencias::SURDEZ => 'Surdez',
            Deficiencias::SURDOCEGUEIRA => 'Surdocegueira',
            Deficiencias::VISAO_MONOCULAR => 'Deficiência Visual',
            Deficiencias::OUTRAS => 'Outras',
        ];

        foreach ($deficiencies as $id => $name) {
            LegacyDeficiency::updateOrCreate([
                'deficiencia_educacenso' => $id,
            ], [
                'nm_deficiencia' => Str::upper($name),
                'deficiency_type_id' => DeficiencyType::DEFICIENCY,
            ]);
        }

        $disorders = [
            Transtornos::DISCALCULIA => 'Discalculia',
            Transtornos::DISGRAFIA => 'Disgrafia / Disortografia',
            Transtornos::DISLALIA => 'Dislalia',
            Transtornos::DISLEXIA => 'Dislexia',
            Transtornos::TDAH => 'TDAH',
            Transtornos::TPAC => 'TPAC',
            Transtornos::OUTROS => 'Outros',
        ];

        foreach ($disorders as $id => $name) {
            LegacyDeficiency::updateOrCreate([
                'transtorno_educacenso' => $id,
            ], [
                'nm_deficiencia' => Str::upper($name),
                'deficiency_type_id' => DeficiencyType::DISORDER,
            ]);
        }
    }
}
