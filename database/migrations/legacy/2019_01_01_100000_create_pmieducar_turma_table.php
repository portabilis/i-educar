<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarTurmaTable extends Migration
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
                
                CREATE SEQUENCE pmieducar.turma_cod_turma_seq
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.turma (
                    cod_turma integer DEFAULT nextval(\'pmieducar.turma_cod_turma_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    ref_ref_cod_serie integer,
                    ref_ref_cod_escola integer,
                    ref_cod_infra_predio_comodo integer,
                    nm_turma character varying(255) NOT NULL,
                    sgl_turma character varying(15),
                    max_aluno integer NOT NULL,
                    multiseriada smallint DEFAULT (0)::smallint NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ref_cod_turma_tipo integer NOT NULL,
                    hora_inicial time without time zone,
                    hora_final time without time zone,
                    hora_inicio_intervalo time without time zone,
                    hora_fim_intervalo time without time zone,
                    ref_cod_regente integer,
                    ref_cod_instituicao_regente integer,
                    ref_cod_instituicao integer,
                    ref_cod_curso integer,
                    ref_ref_cod_serie_mult integer,
                    ref_ref_cod_escola_mult integer,
                    visivel boolean,
                    tipo_boletim integer,
                    turma_turno_id integer,
                    ano integer,
                    tipo_atendimento smallint,
                    turma_mais_educacao smallint,
                    atividade_complementar_1 integer,
                    atividade_complementar_2 integer,
                    atividade_complementar_3 integer,
                    atividade_complementar_4 integer,
                    atividade_complementar_5 integer,
                    atividade_complementar_6 integer,
                    aee_braille smallint,
                    aee_recurso_optico smallint,
                    aee_estrategia_desenvolvimento smallint,
                    aee_tecnica_mobilidade smallint,
                    aee_libras smallint,
                    aee_caa smallint,
                    aee_curricular smallint,
                    aee_soroban smallint,
                    aee_informatica smallint,
                    aee_lingua_escrita smallint,
                    aee_autonomia smallint,
                    cod_curso_profissional integer,
                    etapa_educacenso smallint,
                    ref_cod_disciplina_dispensada integer,
                    parecer_1_etapa text,
                    parecer_2_etapa text,
                    parecer_3_etapa text,
                    parecer_4_etapa text,
                    nao_informar_educacenso smallint,
                    tipo_mediacao_didatico_pedagogico integer,
                    dias_semana integer[],
                    atividades_complementares integer[],
                    atividades_aee integer[],
                    tipo_boletim_diferenciado int2 NULL,
                    local_funcionamento_diferenciado int2 NULL,
	                updated_at timestamp NULL DEFAULT now()
                );
                
                ALTER TABLE ONLY pmieducar.turma
                    ADD CONSTRAINT turma_pkey PRIMARY KEY (cod_turma);

                CREATE INDEX i_turma_nm_turma ON pmieducar.turma USING btree (nm_turma);

                SELECT pg_catalog.setval(\'pmieducar.turma_cod_turma_seq\', 2, true);
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
        Schema::dropIfExists('pmieducar.turma');

        DB::unprepared('DROP SEQUENCE pmieducar.turma_cod_turma_seq;');
    }
}
