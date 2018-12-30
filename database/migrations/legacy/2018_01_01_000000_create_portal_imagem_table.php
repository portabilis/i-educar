<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalImagemTable extends Migration
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

                CREATE SEQUENCE portal.imagem_cod_imagem_seq
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE portal.imagem (
                    cod_imagem integer DEFAULT nextval(\'portal.imagem_cod_imagem_seq\'::regclass) NOT NULL,
                    ref_cod_imagem_tipo integer NOT NULL,
                    caminho character varying(255) NOT NULL,
                    nm_imagem character varying(100),
                    extensao character(3) NOT NULL,
                    altura integer,
                    largura integer,
                    data_cadastro timestamp without time zone NOT NULL,
                    ref_cod_pessoa_cad integer NOT NULL,
                    data_exclusao timestamp without time zone,
                    ref_cod_pessoa_exc integer
                );
                
                SELECT pg_catalog.setval(\'portal.imagem_cod_imagem_seq\', 186, true);
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
        Schema::dropIfExists('portal.imagem');
    }
}
