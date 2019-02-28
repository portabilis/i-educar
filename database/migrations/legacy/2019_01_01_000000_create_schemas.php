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
                CREATE SCHEMA consistenciacao;
                CREATE SCHEMA conv_functions;
                CREATE SCHEMA historico;
                CREATE SCHEMA modules;
                CREATE SCHEMA pmiacoes;
                CREATE SCHEMA pmicontrolesis;
                CREATE SCHEMA pmidrh;
                CREATE SCHEMA pmieducar;
                CREATE SCHEMA pmiotopic;
                CREATE SCHEMA portal;
                CREATE SCHEMA relatorio;
                CREATE SCHEMA serieciasc;
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
                DROP SCHEMA consistenciacao;
                DROP SCHEMA conv_functions;
                DROP SCHEMA historico;
                DROP SCHEMA modules;
                DROP SCHEMA pmiacoes;
                DROP SCHEMA pmicontrolesis;
                DROP SCHEMA pmidrh;
                DROP SCHEMA pmieducar;
                DROP SCHEMA pmiotopic;
                DROP SCHEMA portal;
                DROP SCHEMA relatorio;
                DROP SCHEMA serieciasc;
                DROP SCHEMA urbano;
            '
        );
    }
}
