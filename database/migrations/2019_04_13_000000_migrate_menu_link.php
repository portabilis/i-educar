<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class MigrateMenuLink extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            "
                update pmicontrolesis.menu 
                set caminho = '/intranet/' || caminho
                where true 
                and caminho is not null
                and caminho <> ''
                and caminho not ilike 'module/%';
            "
        );

        DB::unprepared(
            "
                update pmicontrolesis.menu 
                set caminho = '/' || caminho
                where true 
                and caminho is not null
                and caminho <> ''
                and caminho ilike 'module/%';
            "
        );
    }
}
