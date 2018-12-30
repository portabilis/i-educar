<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateConsistenciacaoCampoConsistenciacaoTable extends Migration
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
                
                CREATE TABLE consistenciacao.campo_consistenciacao (
                    idcam numeric(3,0) NOT NULL,
                    campo character varying(50) NOT NULL,
                    permite_regra_cadastrada character(1) NOT NULL,
                    tamanho_maximo numeric(4,0),
                    CONSTRAINT ck_campo_consistenciacao_permite_regra CHECK (((permite_regra_cadastrada = \'S\'::bpchar) OR (permite_regra_cadastrada = \'N\'::bpchar)))
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
        Schema::dropIfExists('consistenciacao.campo_consistenciacao');
    }
}
