<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateConsistenciacaoConfrontacaoTable extends Migration
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
                
                CREATE TABLE consistenciacao.confrontacao (
                    idcon integer DEFAULT nextval(\'consistenciacao.confrontacao_idcon_seq\'::regclass) NOT NULL,
                    idins integer NOT NULL,
                    idpes integer NOT NULL,
                    idmet integer NOT NULL,
                    arquivo_fonte_dados character varying(250) NOT NULL,
                    ignorar_reg_fonte date,
                    desconsiderar_reg_cred_maxima date,
                    data_hora timestamp without time zone NOT NULL
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
        Schema::dropIfExists('consistenciacao.confrontacao');
    }
}
