<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultPmieducarAbandonoTipoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pmieducar.abandono_tipo')->insert([
            'cod_abandono_tipo' => 1,
            'ref_cod_instituicao' => 1,
            'nome' => 'Desistência',
        ]);

        DB::table('pmieducar.abandono_tipo')->insert([
            'cod_abandono_tipo' => 2,
            'ref_cod_instituicao' => 1,
            'nome' => 'Falecimento',
        ]);
    }
}
