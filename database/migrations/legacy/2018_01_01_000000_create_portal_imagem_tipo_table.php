<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalImagemTipoTable extends Migration
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

                CREATE SEQUENCE portal.imagem_tipo_cod_imagem_tipo_seq
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE portal.imagem_tipo (
                    cod_imagem_tipo integer DEFAULT nextval(\'portal.imagem_tipo_cod_imagem_tipo_seq\'::regclass) NOT NULL,
                    nm_tipo character varying(100) NOT NULL
                );
                
                SELECT pg_catalog.setval(\'portal.imagem_tipo_cod_imagem_tipo_seq\', 6, true);
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
        Schema::dropIfExists('portal.imagem_tipo');
    }
}
