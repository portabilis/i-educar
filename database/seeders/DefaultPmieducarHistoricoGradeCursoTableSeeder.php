<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultPmieducarHistoricoGradeCursoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pmieducar.historico_grade_curso')->insert([
            'id' => 1,
            'descricao_etapa' => 'Série',
            'created_at' => now(),
        ]);

        DB::table('pmieducar.historico_grade_curso')->insert([
            'id' => 2,
            'descricao_etapa' => 'Ano',
            'created_at' => now(),
        ]);

        DB::table('pmieducar.historico_grade_curso')->insert([
            'id' => 3,
            'descricao_etapa' => 'EJA',
            'created_at' => now(),
        ]);
    }
}
