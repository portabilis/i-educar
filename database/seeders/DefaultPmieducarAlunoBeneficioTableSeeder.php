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
        DB::table('pmieducar.aluno_beneficio')->updateOrInsert(
            [
                'nm_beneficio' => 'Bolsa Família',
            ],
            [
                'ref_usuario_cad' => 1,
                'data_cadastro' => now(),
                'ativo' => 1,
            ]
        );
    }
}
