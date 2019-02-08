<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateConsistenciacaoOcorrenciaRegraCampoTable extends Migration
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
                
                CREATE TABLE consistenciacao.ocorrencia_regra_campo (
                    idreg integer NOT NULL,
                    conteudo_padrao character varying(60) NOT NULL,
                    ocorrencias text NOT NULL
                );
                
                ALTER TABLE ONLY consistenciacao.ocorrencia_regra_campo
                    ADD CONSTRAINT pk_ocorrencia_regra_campo PRIMARY KEY (idreg, conteudo_padrao);
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
        Schema::dropIfExists('consistenciacao.ocorrencia_regra_campo');
    }
}
