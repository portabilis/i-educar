<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosContratoTable extends Migration
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
                
                CREATE TABLE alimentos.contrato (
                    idcon integer DEFAULT nextval(\'alimentos.contrato_idcon_seq\'::regclass) NOT NULL,
                    codigo character varying(20) NOT NULL,
                    idcli character varying(10) NOT NULL,
                    login character varying(80) NOT NULL,
                    num_aditivo integer NOT NULL,
                    idfor integer NOT NULL,
                    dt_vigencia date NOT NULL,
                    tipo character(1) NOT NULL,
                    vlr_atual numeric NOT NULL,
                    cancelado character(1) NOT NULL,
                    dt_cancelamento timestamp without time zone,
                    dt_inclusao timestamp without time zone NOT NULL,
                    ultimo_contrato character(1) NOT NULL,
                    vlr_original numeric NOT NULL,
                    finalizado character(1) NOT NULL,
                    CONSTRAINT ck_contrato_cancelado CHECK (((cancelado = \'S\'::bpchar) OR (cancelado = \'N\'::bpchar))),
                    CONSTRAINT ck_contrato_finalizado CHECK (((finalizado = \'S\'::bpchar) OR (finalizado = \'N\'::bpchar))),
                    CONSTRAINT ck_contrato_tipo CHECK (((tipo = \'C\'::bpchar) OR (tipo = \'A\'::bpchar))),
                    CONSTRAINT ck_contrato_ultimo_contrato CHECK (((ultimo_contrato = \'S\'::bpchar) OR (ultimo_contrato = \'N\'::bpchar)))
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
        Schema::dropIfExists('alimentos.contrato');
    }
}
