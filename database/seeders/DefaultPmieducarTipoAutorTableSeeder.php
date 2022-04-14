<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultPmieducarTipoAutorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pmieducar.tipo_autor')->insert([
            'codigo' => 1,
            'tipo_autor' => 'Autor',
        ]);

        DB::table('pmieducar.tipo_autor')->insert([
            'codigo' => 2,
            'tipo_autor' => 'Evento',
        ]);

        DB::table('pmieducar.tipo_autor')->insert([
            'codigo' => 3,
            'tipo_autor' => 'Entidade coletiva',
        ]);

        DB::table('pmieducar.tipo_autor')->insert([
            'codigo' => 4,
            'tipo_autor' => 'Anônimo',
        ]);
    }
}
