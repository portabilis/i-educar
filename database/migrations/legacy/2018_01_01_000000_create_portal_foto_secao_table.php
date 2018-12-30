<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalFotoSecaoTable extends Migration
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
                SET default_with_oids = true;

                CREATE SEQUENCE portal.foto_secao_cod_foto_secao_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE portal.foto_secao (
                    cod_foto_secao integer DEFAULT nextval(\'portal.foto_secao_cod_foto_secao_seq\'::regclass) NOT NULL,
                    nm_secao character varying(255)
                );
                
                ALTER TABLE ONLY portal.foto_secao
                    ADD CONSTRAINT foto_secao_pk PRIMARY KEY (cod_foto_secao);

                SELECT pg_catalog.setval(\'portal.foto_secao_cod_foto_secao_seq\', 1, false);
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
        Schema::dropIfExists('portal.foto_secao');

        DB::unprepared('DROP SEQUENCE portal.foto_secao_cod_foto_secao_seq;');
    }
}
