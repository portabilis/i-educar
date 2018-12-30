<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateConsistenciacaoCampoMetadadoTable extends Migration
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
                
                CREATE TABLE consistenciacao.campo_metadado (
                    id_campo_met integer DEFAULT nextval(\'consistenciacao.campo_metadado_id_campo_met_seq\'::regclass) NOT NULL,
                    idmet integer NOT NULL,
                    idreg integer,
                    idcam numeric(3,0),
                    posicao_inicial numeric(5,0),
                    posicao_final numeric(5,0),
                    posicao_coluna numeric(5,0),
                    credibilidade numeric(1,0) NOT NULL,
                    data_atualizacao character(1) NOT NULL,
                    CONSTRAINT ck_cam_met_campo_cred CHECK (((credibilidade >= (2)::numeric) AND (credibilidade <= (4)::numeric))),
                    CONSTRAINT ck_cam_met_data_atualizacao CHECK (((data_atualizacao = \'S\'::bpchar) OR (data_atualizacao = \'N\'::bpchar)))
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
        Schema::dropIfExists('consistenciacao.campo_metadado');
    }
}
