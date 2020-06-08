<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarCursoTable extends Migration
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
                
                CREATE SEQUENCE pmieducar.curso_cod_curso_seq
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.curso (
                    cod_curso integer DEFAULT nextval(\'pmieducar.curso_cod_curso_seq\'::regclass) NOT NULL,
                    ref_usuario_cad integer NOT NULL,
                    ref_cod_tipo_regime integer,
                    ref_cod_nivel_ensino integer NOT NULL,
                    ref_cod_tipo_ensino integer NOT NULL,
                    nm_curso character varying(255) NOT NULL,
                    sgl_curso character varying(15) NOT NULL,
                    qtd_etapas smallint NOT NULL,
                    carga_horaria double precision NOT NULL,
                    ato_poder_publico character varying(255),
                    objetivo_curso text,
                    publico_alvo text,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ref_usuario_exc integer,
                    ref_cod_instituicao integer NOT NULL,
                    padrao_ano_escolar smallint DEFAULT (0)::smallint NOT NULL,
                    hora_falta double precision DEFAULT 0.00 NOT NULL,
                    multi_seriado integer,
                    modalidade_curso integer,
	                updated_at timestamp NULL DEFAULT now()
                );
                
                ALTER TABLE ONLY pmieducar.curso
                    ADD CONSTRAINT curso_pkey PRIMARY KEY (cod_curso);

                CREATE INDEX i_curso_ativo ON pmieducar.curso USING btree (ativo);

                CREATE INDEX i_curso_ato_poder_publico ON pmieducar.curso USING btree (ato_poder_publico);

                CREATE INDEX i_curso_carga_horaria ON pmieducar.curso USING btree (carga_horaria);

                CREATE INDEX i_curso_nm_curso ON pmieducar.curso USING btree (nm_curso);

                CREATE INDEX i_curso_objetivo_curso ON pmieducar.curso USING btree (objetivo_curso);

                CREATE INDEX i_curso_qtd_etapas ON pmieducar.curso USING btree (qtd_etapas);

                CREATE INDEX i_curso_ref_cod_nivel_ensino ON pmieducar.curso USING btree (ref_cod_nivel_ensino);

                CREATE INDEX i_curso_ref_cod_tipo_ensino ON pmieducar.curso USING btree (ref_cod_tipo_ensino);

                CREATE INDEX i_curso_ref_cod_tipo_regime ON pmieducar.curso USING btree (ref_cod_tipo_regime);

                CREATE INDEX i_curso_ref_usuario_cad ON pmieducar.curso USING btree (ref_usuario_cad);

                CREATE INDEX i_curso_sgl_curso ON pmieducar.curso USING btree (sgl_curso);

                SELECT pg_catalog.setval(\'pmieducar.curso_cod_curso_seq\', 1, true);
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
        Schema::dropIfExists('pmieducar.curso');

        DB::unprepared('DROP SEQUENCE pmieducar.curso_cod_curso_seq;');
    }
}
