<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateExtensions extends Migration
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
                CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;

                CREATE EXTENSION IF NOT EXISTS unaccent WITH SCHEMA public;

                CREATE EXTENSION IF NOT EXISTS hstore WITH SCHEMA relatorio;
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
                DROP EXTENSION hstore;

                DROP EXTENSION plpgsql;

                DROP EXTENSION unaccent;
            '
        );
    }
}
