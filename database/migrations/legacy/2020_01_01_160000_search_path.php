<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SearchPath extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $database = DB::selectOne('select current_database() as database')->database;

        DB::unprepared(
            '
                ALTER DATABASE ' . $database . ' 
                SET search_path = "$user", public, portal, cadastro, 
                pmieducar, urbano, modules;
            '
        );
    }
}
