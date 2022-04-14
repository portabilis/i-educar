<?php

namespace Database\Seeders;

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultPmieducarTipoUsuarioTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pmieducar.tipo_usuario')->insert([
            'cod_tipo_usuario' => 1,
            'nm_tipo' => 'Administrador',
            'nivel' => 1,
            'ref_funcionario_cad' => 1,
            'data_cadastro' => now(),
        ]);

        DB::table('pmieducar.tipo_usuario')->insert([
            'cod_tipo_usuario' => 2,
            'nm_tipo' => 'Professor',
            'nivel' => 4,
            'ref_funcionario_cad' => 1,
            'data_cadastro' => now(),
        ]);

        DB::table('pmieducar.tipo_usuario')->insert([
            'cod_tipo_usuario' => 3,
            'nm_tipo' => 'Aluno',
            'nivel' => 4,
            'ref_funcionario_cad' => 1,
            'data_cadastro' => now(),
        ]);
    }
}
