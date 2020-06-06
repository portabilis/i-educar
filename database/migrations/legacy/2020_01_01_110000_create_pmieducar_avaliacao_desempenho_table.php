<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarAvaliacaoDesempenhoTable extends Migration
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
                
                CREATE TABLE pmieducar.avaliacao_desempenho (
                    sequencial integer NOT NULL,
                    ref_cod_servidor integer NOT NULL,
                    ref_ref_cod_instituicao integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    descricao text NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    titulo_avaliacao character varying(255) NOT NULL
                );
                
                ALTER TABLE ONLY pmieducar.avaliacao_desempenho
                    ADD CONSTRAINT avaliacao_desempenho_pkey PRIMARY KEY (sequencial, ref_cod_servidor, ref_ref_cod_instituicao);
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
        Schema::dropIfExists('pmieducar.avaliacao_desempenho');
    }
}
