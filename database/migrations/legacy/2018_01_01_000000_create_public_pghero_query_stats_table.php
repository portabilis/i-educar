<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePublicPgheroQueryStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        # FIXME

        DB::unprepared(
            '
                SET default_with_oids = false;
                
                CREATE TABLE public.pghero_query_stats (
                    id integer NOT NULL,
                    database text,
                    "user" text,
                    query text,
                    query_hash bigint,
                    total_time double precision,
                    calls bigint,
                    captured_at timestamp without time zone
                );
                
                -- ALTER SEQUENCE public.pghero_query_stats_id_seq OWNED BY public.pghero_query_stats.id;
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
        Schema::dropIfExists('public.pghero_query_stats');
    }
}
