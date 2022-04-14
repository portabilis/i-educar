<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateSchemas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            '
                CREATE SCHEMA cadastro;
                CREATE SCHEMA modules;
                CREATE SCHEMA pmieducar;
                CREATE SCHEMA portal;
                CREATE SCHEMA relatorio;
                CREATE SCHEMA urbano;
            '
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared(
            '
                DROP SCHEMA cadastro;
                DROP SCHEMA modules;
                DROP SCHEMA pmieducar;
                DROP SCHEMA portal;
                DROP SCHEMA relatorio;
                DROP SCHEMA urbano;
            '
        );
    }
}
