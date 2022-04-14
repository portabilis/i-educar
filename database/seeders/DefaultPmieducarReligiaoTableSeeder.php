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
        DB::table('pmieducar.religiao')->updateOrInsert(
            [
                'nm_religiao' => 'Católico'
            ],
            [
                'ref_usuario_cad' => 1,
                'data_cadastro' => now(),
                'ativo' => 1,
            ]
        );

        DB::table('pmieducar.religiao')->updateOrInsert(
            [
                'nm_religiao' => 'Evangélico'
            ],
            [
                'ref_usuario_cad' => 1,
                'data_cadastro' => now(),
                'ativo' => 1,
            ]
        );

        DB::table('pmieducar.religiao')->updateOrInsert(
            [
                'nm_religiao' => 'Outros',
            ],
            [
                'ref_usuario_cad' => 1,
                'data_cadastro' => now(),
                'ativo' => 1,
            ]
        );
    }
}
