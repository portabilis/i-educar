<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultCadastroCodigoCartorioInepTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared(
            file_get_contents(__DIR__ . '/../sqls/inserts/cadastro.codigo_cartorio_inep.sql')
        );
    }
}
