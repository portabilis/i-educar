<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultPortalAgendaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('portal.agenda')->insert([
            'cod_agenda' => 1,
            'ref_ref_cod_pessoa_cad' => 1,
            'nm_agenda' => 'Administrador',
            'data_cad' => now(),
        ]);
    }
}
