<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarServidorAfastamentoTable extends Migration
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
                CREATE TABLE pmieducar.servidor_afastamento (
                    ref_cod_servidor integer NOT NULL,
                    sequencial integer NOT NULL,
                    ref_ref_cod_instituicao integer NOT NULL,
                    ref_cod_motivo_afastamento integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    data_retorno timestamp without time zone,
                    data_saida timestamp without time zone NOT NULL,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
                );

                ALTER TABLE ONLY pmieducar.servidor_afastamento
                    ADD CONSTRAINT servidor_afastamento_pkey PRIMARY KEY (ref_cod_servidor, sequencial, ref_ref_cod_instituicao);
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
        Schema::dropIfExists('pmieducar.servidor_afastamento');
    }
}
