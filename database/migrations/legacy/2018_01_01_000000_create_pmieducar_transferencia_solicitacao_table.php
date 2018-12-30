<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarTransferenciaSolicitacaoTable extends Migration
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

                CREATE TABLE pmieducar.transferencia_solicitacao (
                    cod_transferencia_solicitacao integer DEFAULT nextval(\'pmieducar.transferencia_solicitacao_cod_transferencia_solicitacao_seq\'::regclass) NOT NULL,
                    ref_cod_transferencia_tipo integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    ref_cod_matricula_entrada integer,
                    ref_cod_matricula_saida integer NOT NULL,
                    observacao text,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    data_transferencia timestamp without time zone,
                    ref_cod_escola_destino integer,
                    escola_destino_externa character varying,
                    estado_escola_destino_externa character varying(60),
                    municipio_escola_destino_externa character varying(60)
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
        Schema::dropIfExists('pmieducar.transferencia_solicitacao');
    }
}
