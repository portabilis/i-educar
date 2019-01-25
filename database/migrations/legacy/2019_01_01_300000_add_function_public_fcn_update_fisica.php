<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFunctionPublicFcnUpdateFisica extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            file_get_contents(__DIR__ . '/../../sqls/functions/public.fcn_update_fisica.sql')
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
            'DROP FUNCTION public.fcn_update_fisica(integer, character varying, character varying, integer, integer, integer, integer, integer, integer, character varying, character varying, integer, integer, character varying, integer, character varying, integer, character varying, character varying, character varying, character varying, character varying, character, integer, integer);'
        );
    }
}
