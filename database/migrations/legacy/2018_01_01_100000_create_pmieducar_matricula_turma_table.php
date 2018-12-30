<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarMatriculaTurmaTable extends Migration
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
                
                CREATE TABLE pmieducar.matricula_turma (
                    ref_cod_matricula integer NOT NULL,
                    ref_cod_turma integer NOT NULL,
                    sequencial integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    data_enturmacao date NOT NULL,
                    sequencial_fechamento integer DEFAULT 0 NOT NULL,
                    transferido boolean,
                    remanejado boolean,
                    reclassificado boolean,
                    abandono boolean,
                    updated_at timestamp without time zone,
                    falecido boolean,
                    etapa_educacenso smallint,
                    turma_unificada smallint
                );
                
                ALTER TABLE ONLY pmieducar.matricula_turma
                    ADD CONSTRAINT matricula_turma_pkey PRIMARY KEY (ref_cod_matricula, ref_cod_turma, sequencial);
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
        Schema::dropIfExists('pmieducar.matricula_turma');
    }
}
