<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateConsistenciacaoIncoerenciaFoneTable extends Migration
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
                
                CREATE SEQUENCE consistenciacao.incoerencia_fone_id_inc_fone_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE consistenciacao.incoerencia_fone (
                    id_inc_fone integer DEFAULT nextval(\'consistenciacao.incoerencia_fone_id_inc_fone_seq\'::regclass) NOT NULL,
                    idinc integer NOT NULL,
                    tipo character varying(60) NOT NULL,
                    ddd numeric(3,0),
                    fone numeric(8,0),
                    CONSTRAINT ck_incoerencia_fone_tipo CHECK ((((tipo)::text >= ((1)::numeric)::text) AND ((tipo)::text <= ((4)::numeric)::text)))
                );
                
                SELECT pg_catalog.setval(\'consistenciacao.incoerencia_fone_id_inc_fone_seq\', 1, false);
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
        Schema::dropIfExists('consistenciacao.incoerencia_fone');
    }
}
