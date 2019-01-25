<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateConsistenciacaoHistoricoCampoTable extends Migration
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
                
                CREATE TABLE consistenciacao.historico_campo (
                    idpes numeric(8,0) NOT NULL,
                    idcam numeric(3,0) NOT NULL,
                    credibilidade numeric(1,0) NOT NULL,
                    data_hora timestamp without time zone NOT NULL,
                    CONSTRAINT ck_historico_campo_cred CHECK (((credibilidade >= (1)::numeric) AND (credibilidade <= (5)::numeric)))
                );
                
                ALTER TABLE ONLY consistenciacao.historico_campo
                    ADD CONSTRAINT pk_historico_campo PRIMARY KEY (idpes, idcam);
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
        Schema::dropIfExists('consistenciacao.historico_campo');
    }
}
