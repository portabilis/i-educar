<?php

namespace Database\Seeders;

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultPmieducarModuloTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pmieducar.modulo')->updateOrInsert([
            'cod_modulo' => 1,
            'ref_usuario_cad' => 1,
            'nm_tipo' => 'Bimestre',
            'data_cadastro' => now(),
            'ativo' => 1,
            'ref_cod_instituicao' => 1,
            'num_etapas' => 4
        ]);
    }
}
