<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAggregates extends Migration
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
                CREATE AGGREGATE public.textcat_all(text) (
                    SFUNC = public.commacat_ignore_nulls,
                    STYPE = text,
                    INITCOND = \'\'
                );
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
                DROP TYPE public.typ_idlog;
                
                DROP TYPE public.typ_idpes;
            '
        );
    }
}
