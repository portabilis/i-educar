<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

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
                COMMENT ON EXTENSION plpgsql IS \'PL/pgSQL procedural language\';
                
                CREATE EXTENSION IF NOT EXISTS unaccent WITH SCHEMA public;
                COMMENT ON EXTENSION unaccent IS \'text search dictionary that removes accents\';
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
                DROP EXTENSION plpgsql;
                
                DROP EXTENSION unaccent;
            '
        );
    }
}
