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
                    modalidade_curso integer
                );
                
                ALTER TABLE ONLY pmieducar.curso
                    ADD CONSTRAINT curso_pkey PRIMARY KEY (cod_curso);

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
