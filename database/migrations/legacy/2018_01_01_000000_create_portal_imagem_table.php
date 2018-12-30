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
