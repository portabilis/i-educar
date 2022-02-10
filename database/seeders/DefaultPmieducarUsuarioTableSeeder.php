<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultPmieducarUsuarioTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pmieducar.usuario')->insert([
            'cod_usuario' => 1,
            'ref_cod_tipo_usuario' => 1,
            'ref_funcionario_cad' => 1,
            'data_cadastro' => now(),
        ]);

        DB::table('pmieducar.usuario')->insert([
            'cod_usuario' => 2,
            'ref_cod_tipo_usuario' => 1,
            'ref_funcionario_cad' => 1,
            'data_cadastro' => now(),
        ]);
    }
}
