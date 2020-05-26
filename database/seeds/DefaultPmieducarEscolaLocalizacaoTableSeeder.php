<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultPmieducarEscolaLocalizacaoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pmieducar.escola_localizacao')->insert([
            'cod_escola_localizacao' => 1,
            'ref_usuario_cad' => 1,
            'nm_localizacao' => 'Urbana',
            'data_cadastro' => now(),
            'ref_cod_instituicao' => 1,
        ]);

        DB::table('pmieducar.escola_localizacao')->insert([
            'cod_escola_localizacao' => 2,
            'ref_usuario_cad' => 1,
            'nm_localizacao' => 'Rural',
            'data_cadastro' => now(),
            'ref_cod_instituicao' => 1,
        ]);
    }
}
