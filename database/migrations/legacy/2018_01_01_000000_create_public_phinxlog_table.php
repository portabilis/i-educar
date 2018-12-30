<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePublicPhinxlogTable extends Migration
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
                
                CREATE TABLE public.phinxlog (
                    version bigint NOT NULL,
                    migration_name character varying(100),
                    start_time timestamp without time zone,
                    end_time timestamp without time zone,
                    breakpoint boolean DEFAULT false NOT NULL
                );
                
                ALTER TABLE ONLY public.phinxlog
                    ADD CONSTRAINT phinxlog_pkey PRIMARY KEY (version);
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
        Schema::dropIfExists('public.phinxlog');
    }
}
