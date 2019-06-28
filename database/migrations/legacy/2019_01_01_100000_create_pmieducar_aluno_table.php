<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarAlunoTable extends Migration
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
                
                CREATE SEQUENCE pmieducar.aluno_cod_aluno_seq
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.aluno (
                    cod_aluno integer DEFAULT nextval(\'pmieducar.aluno_cod_aluno_seq\'::regclass) NOT NULL,
                    ref_cod_religiao integer,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer,
                    ref_idpes integer,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    caminho_foto character varying(255),
                    analfabeto smallint DEFAULT (0)::smallint,
                    nm_pai character varying(255),
                    nm_mae character varying(255),
                    tipo_responsavel character(1),
                    aluno_estado_id character varying(25),
                    justificativa_falta_documentacao smallint,
                    url_laudo_medico json,
                    codigo_sistema character varying(30),
                    veiculo_transporte_escolar int4[] NULL,
                    autorizado_um character varying(150),
                    parentesco_um character varying(150),
                    autorizado_dois character varying(150),
                    parentesco_dois character varying(150),
                    autorizado_tres character varying(150),
                    parentesco_tres character varying(150),
                    autorizado_quatro character varying(150),
                    parentesco_quatro character varying(150),
                    autorizado_cinco character varying(150),
                    parentesco_cinco character varying(150),
                    url_documento json,
	                recebe_escolarizacao_em_outro_espaco int2 NOT NULL DEFAULT 1,
                    recursos_prova_inep integer[],
	                updated_at timestamp NULL DEFAULT now()
                );
                
                ALTER TABLE ONLY pmieducar.aluno
                    ADD CONSTRAINT aluno_pkey PRIMARY KEY (cod_aluno);

                ALTER TABLE ONLY pmieducar.aluno
                    ADD CONSTRAINT aluno_ref_idpes_un UNIQUE (ref_idpes);

                CREATE INDEX i_aluno_ativo ON pmieducar.aluno USING btree (ativo);

                CREATE INDEX i_aluno_ref_cod_religiao ON pmieducar.aluno USING btree (ref_cod_religiao);

                CREATE INDEX i_aluno_ref_idpes ON pmieducar.aluno USING btree (ref_idpes);

                CREATE INDEX i_aluno_ref_usuario_cad ON pmieducar.aluno USING btree (ref_usuario_cad);

                SELECT pg_catalog.setval(\'pmieducar.aluno_cod_aluno_seq\', 2, true);
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
        Schema::dropIfExists('pmieducar.aluno');

        DB::unprepared('DROP SEQUENCE pmieducar.aluno_cod_aluno_seq;');
    }
}
