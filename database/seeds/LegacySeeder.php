<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LegacySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared(
            file_get_contents(__DIR__ . '/../ieducar.sql')
        );

        DB::unprepared(
            '
                ALTER DATABASE ' . env('DB_DATABASE') . ' 
                SET search_path = "$user", public, portal, cadastro, acesso, alimentos, consistenciacao,
                historico, pmiacoes, pmicontrolesis, pmidrh, pmieducar, pmiotopic, urbano, modules;
            '
        );
    }
}
