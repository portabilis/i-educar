<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateConsistenciacaoRegraCampoTable extends Migration
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
                
                CREATE SEQUENCE consistenciacao.regra_campo_idreg_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE consistenciacao.regra_campo (
                    idreg integer DEFAULT nextval(\'consistenciacao.regra_campo_idreg_seq\'::regclass) NOT NULL,
                    nome character varying(60) NOT NULL,
                    tipo character(1) NOT NULL,
                    CONSTRAINT ck_regra_campo_tipo CHECK (((tipo = \'S\'::bpchar) OR (tipo = \'N\'::bpchar)))
                );
                
                SELECT pg_catalog.setval(\'consistenciacao.regra_campo_idreg_seq\', 1, false);
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
        Schema::dropIfExists('consistenciacao.regra_campo');
    }
}
