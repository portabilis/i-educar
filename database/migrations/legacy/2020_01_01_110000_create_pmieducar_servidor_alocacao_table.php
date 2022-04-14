<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarServidorAlocacaoTable extends Migration
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
                CREATE SEQUENCE pmieducar.servidor_alocacao_cod_servidor_alocacao_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.servidor_alocacao (
                    cod_servidor_alocacao integer DEFAULT nextval(\'pmieducar.servidor_alocacao_cod_servidor_alocacao_seq\'::regclass) NOT NULL,
                    ref_ref_cod_instituicao integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    ref_cod_escola integer NOT NULL,
                    ref_cod_servidor integer NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    carga_horaria interval,
                    periodo smallint DEFAULT (1)::smallint,
                    hora_final time without time zone,
                    hora_inicial time without time zone,
                    dia_semana integer,
                    ref_cod_servidor_funcao integer,
                    ref_cod_funcionario_vinculo integer,
                    ano integer,
                    data_admissao date,
                    hora_atividade time without time zone,
                    horas_excedentes time without time zone,
                    data_saida date NULL
                );

                ALTER TABLE ONLY pmieducar.servidor_alocacao
                    ADD CONSTRAINT servidor_alocacao_pkey PRIMARY KEY (cod_servidor_alocacao);

                CREATE INDEX servidor_alocacao_busca_horarios_idx ON pmieducar.servidor_alocacao USING btree (ref_ref_cod_instituicao, ref_cod_escola, ativo, periodo, carga_horaria);

                SELECT pg_catalog.setval(\'pmieducar.servidor_alocacao_cod_servidor_alocacao_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.servidor_alocacao');

        DB::unprepared('DROP SEQUENCE pmieducar.servidor_alocacao_cod_servidor_alocacao_seq;');
    }
}
