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
        DB::unprepared(
            '
                SET default_with_oids = false;
                
                CREATE SEQUENCE public.pghero_query_stats_id_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

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
                
                ALTER SEQUENCE public.pghero_query_stats_id_seq OWNED BY public.pghero_query_stats.id;
                
                ALTER TABLE ONLY public.pghero_query_stats
                    ADD CONSTRAINT pghero_query_stats_pkey PRIMARY KEY (id);

                ALTER TABLE ONLY public.pghero_query_stats ALTER COLUMN id SET DEFAULT nextval(\'public.pghero_query_stats_id_seq\'::regclass);

                SELECT pg_catalog.setval(\'public.pghero_query_stats_id_seq\', 1, false);
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
