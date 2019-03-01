<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

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
                CREATE SCHEMA acesso;
                CREATE SCHEMA cadastro;
                CREATE SCHEMA historico;
                CREATE SCHEMA modules;
                CREATE SCHEMA pmicontrolesis;
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
                DROP SCHEMA acesso;
                DROP SCHEMA cadastro;
                DROP SCHEMA historico;
                DROP SCHEMA modules;
                DROP SCHEMA pmicontrolesis;
                DROP SCHEMA pmieducar;
                DROP SCHEMA portal;
                DROP SCHEMA relatorio;
                DROP SCHEMA urbano;
            '
        );
    }
}
