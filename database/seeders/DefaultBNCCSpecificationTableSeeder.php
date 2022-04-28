<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultBNCCSpecificationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::raw('TRUNCATE modules.bncc_especificacoes CASCADE;');

        DB::unprepared(
            file_get_contents(__DIR__ . '/../sqls/inserts/bncc_especificacoes.sql')
        );
    }
}
