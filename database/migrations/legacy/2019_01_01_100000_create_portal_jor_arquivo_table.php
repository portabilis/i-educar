<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalJorArquivoTable extends Migration
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

                CREATE TABLE portal.jor_arquivo (
                    ref_cod_jor_edicao integer DEFAULT 0 NOT NULL,
                    jor_arquivo smallint DEFAULT (0)::smallint NOT NULL,
                    jor_caminho character varying(255) DEFAULT \'\'::character varying NOT NULL
                );
                
                ALTER TABLE ONLY portal.jor_arquivo
                    ADD CONSTRAINT jor_arquivo_pk PRIMARY KEY (ref_cod_jor_edicao, jor_arquivo);
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
        Schema::dropIfExists('portal.jor_arquivo');
    }
}
