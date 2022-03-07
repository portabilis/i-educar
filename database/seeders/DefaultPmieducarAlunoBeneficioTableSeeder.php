<?php

namespace Database\Seeders;

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultPmieducarAlunoBeneficioTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pmieducar.aluno_beneficio')->updateOrInsert([
            'cod_aluno_beneficio' => 1,
            'ref_usuario_cad' => 1,
            'nm_beneficio' => 'Bolsa Família',
            'data_cadastro' => now(),
            'ativo' => 1,
        ]);
    }
}
