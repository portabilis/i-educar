<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarHistoricoDisciplinasTable extends Migration
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
                CREATE TABLE pmieducar.historico_disciplinas (
	                id serial NOT NULL,
                    sequencial integer NOT NULL,
                    ref_ref_cod_aluno integer NOT NULL,
                    ref_sequencial integer NOT NULL,
                    nm_disciplina text NOT NULL,
                    nota character varying(255) NOT NULL,
                    faltas integer,
                    import numeric(1,0),
                    ordenamento integer,
                    carga_horaria_disciplina integer,
                    dependencia boolean DEFAULT false,
                    tipo_base int4 NOT NULL DEFAULT 1
                );

                ALTER TABLE ONLY pmieducar.historico_disciplinas
                    ADD CONSTRAINT historico_disciplinas_pkey PRIMARY KEY (id);

                CREATE INDEX idx_historico_disciplinas_id ON pmieducar.historico_disciplinas USING btree (sequencial, ref_ref_cod_aluno, ref_sequencial);

                CREATE INDEX idx_historico_disciplinas_id1 ON pmieducar.historico_disciplinas USING btree (ref_ref_cod_aluno, ref_sequencial);

                CREATE UNIQUE INDEX pmieducar_historico_disciplinas_sequencial_ref_ref_cod_aluno_re ON pmieducar.historico_disciplinas USING btree (sequencial, ref_ref_cod_aluno, ref_sequencial);
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
        Schema::dropIfExists('pmieducar.historico_disciplinas');
    }
}
