<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalAgendaCompromissoTable extends Migration
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

                CREATE TABLE portal.agenda_compromisso (
                    cod_agenda_compromisso integer NOT NULL,
                    versao integer NOT NULL,
                    ref_cod_agenda integer NOT NULL,
                    ref_ref_cod_pessoa_cad integer NOT NULL,
                    ativo smallint DEFAULT 1,
                    data_inicio timestamp without time zone,
                    titulo character varying,
                    descricao text,
                    importante smallint DEFAULT 0 NOT NULL,
                    publico smallint DEFAULT 0 NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_fim timestamp without time zone
                );
                
                ALTER TABLE ONLY portal.agenda_compromisso
                    ADD CONSTRAINT agenda_compromisso_pkey PRIMARY KEY (cod_agenda_compromisso, versao, ref_cod_agenda);
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
        Schema::dropIfExists('portal.agenda_compromisso');
    }
}
