<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarMatriculaTable extends Migration
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
                CREATE SEQUENCE pmieducar.matricula_cod_matricula_seq
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.matricula (
                    cod_matricula integer DEFAULT nextval(\'pmieducar.matricula_cod_matricula_seq\'::regclass) NOT NULL,
                    ref_cod_reserva_vaga integer,
                    ref_ref_cod_escola integer,
                    ref_ref_cod_serie integer,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    ref_cod_aluno integer NOT NULL,
                    aprovado smallint DEFAULT (0)::smallint NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ano integer NOT NULL,
                    ultima_matricula smallint DEFAULT (0)::smallint NOT NULL,
                    modulo smallint DEFAULT 1 NOT NULL,
                    descricao_reclassificacao text,
                    formando smallint DEFAULT (0)::smallint NOT NULL,
                    matricula_reclassificacao smallint DEFAULT (0)::smallint,
                    ref_cod_curso integer,
                    matricula_transferencia boolean DEFAULT false NOT NULL,
                    semestre smallint,
                    observacao character varying(300),
                    data_matricula timestamp without time zone,
                    data_cancel timestamp without time zone,
                    ref_cod_abandono_tipo integer,
                    turno_pre_matricula smallint,
                    dependencia boolean DEFAULT false,
                    saida_escola boolean DEFAULT false,
                    data_saida_escola date,
	                updated_at timestamp NULL DEFAULT now()
                );

                ALTER TABLE ONLY pmieducar.matricula
                    ADD CONSTRAINT matricula_pkey PRIMARY KEY (cod_matricula);

                CREATE INDEX idx_matricula_cod_escola_aluno ON pmieducar.matricula USING btree (ref_ref_cod_escola, ref_cod_aluno);

                CREATE INDEX matricula_ano_idx ON pmieducar.matricula USING btree (ano);

                CREATE INDEX matricula_ativo_idx ON pmieducar.matricula USING btree (ativo);

                SELECT pg_catalog.setval(\'pmieducar.matricula_cod_matricula_seq\', 2, true);
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
        Schema::dropIfExists('pmieducar.matricula');

        DB::unprepared('DROP SEQUENCE pmieducar.matricula_cod_matricula_seq;');
    }
}
