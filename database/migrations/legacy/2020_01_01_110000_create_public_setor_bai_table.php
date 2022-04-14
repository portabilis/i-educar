<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePublicSetorBaiTable extends Migration
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
                CREATE SEQUENCE public.seq_setor_bai
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE public.setor_bai (
                    idsetorbai numeric(6,0) DEFAULT nextval((\'public.seq_setor_bai\'::text)::regclass) NOT NULL,
                    nome character varying(80) NOT NULL
                );

                ALTER TABLE ONLY public.setor_bai
                    ADD CONSTRAINT pk_setorbai PRIMARY KEY (idsetorbai);

                SELECT pg_catalog.setval(\'public.seq_setor_bai\', 1, false);
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
        Schema::dropIfExists('public.setor_bai');

        DB::unprepared('DROP SEQUENCE public.seq_setor_bai;');
    }
}
