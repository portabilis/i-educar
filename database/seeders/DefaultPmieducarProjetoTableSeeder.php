<?php

namespace Database\Seeders;

use App\Models\LegacyProject;
use Illuminate\Database\Seeder;

class DefaultPmieducarProjetoTableSeeder extends Seeder
{
    public function run()
    {
        $projects = [
            'Reforço Escolar',
            'Alfabetização e Letramento',
            'Preparatório (Saeb)',
            'Fanfarra',
            'Teatro',
            'Xadrez',
            'Escolinha de Esportes',
            'Dança',
            'Artes Marciais',
        ];

        foreach ($projects as $project) {
            LegacyProject::updateOrCreate([
                'nome' => $project,
            ], [
                'observacao' => '',
            ]);
        }
    }
}
