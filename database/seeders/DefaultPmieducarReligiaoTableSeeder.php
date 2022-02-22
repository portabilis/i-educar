<?php

namespace Database\Seeders;

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultPmieducarReligiaoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pmieducar.religiao')->updateOrInsert([
            'cod_religiao' => 1,
            'ref_usuario_cad' => 1,
            'nm_religiao' => 'Católico',
            'data_cadastro' => now(),
            'ativo' => 1,
        ]);

        DB::table('pmieducar.religiao')->updateOrInsert([
            'cod_religiao' => 2,
            'ref_usuario_cad' => 1,
            'nm_religiao' => 'Evangélico',
            'data_cadastro' => now(),
            'ativo' => 1,
        ]);

        DB::table('pmieducar.religiao')->updateOrInsert([
            'cod_religiao' => 3,
            'ref_usuario_cad' => 1,
            'nm_religiao' => 'Outros',
            'data_cadastro' => now(),
            'ativo' => 1,
        ]);
    }
}
