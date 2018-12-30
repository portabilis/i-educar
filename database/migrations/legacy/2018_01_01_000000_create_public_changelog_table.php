<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePublicChangelogTable extends Migration
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
                SET default_with_oids = false;
                
                CREATE TABLE public.changelog (
                    change_number bigint NOT NULL,
                    delta_set character varying(10) NOT NULL,
                    start_dt timestamp without time zone NOT NULL,
                    complete_dt timestamp without time zone,
                    applied_by character varying(100) NOT NULL,
                    description character varying(500) NOT NULL
                );
                
                ALTER TABLE ONLY public.changelog
                    ADD CONSTRAINT pkchangelog PRIMARY KEY (change_number, delta_set);
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
        Schema::dropIfExists('public.changelog');
    }
}
