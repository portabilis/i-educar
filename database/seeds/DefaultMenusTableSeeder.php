<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultMenusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared(
            file_get_contents(__DIR__ . '/../sqls/inserts/public.menus.sql')
        );

        DB::unprepared(
            'SELECT pg_catalog.setval(\'menus_id_seq\', 170, true);'
        );
    }
}
