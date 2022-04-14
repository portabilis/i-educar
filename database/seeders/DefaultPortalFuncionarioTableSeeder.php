<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultPortalFuncionarioTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared(
            file_get_contents(__DIR__ . '/../sqls/inserts/portal.funcionario.sql')
        );
    }
}
