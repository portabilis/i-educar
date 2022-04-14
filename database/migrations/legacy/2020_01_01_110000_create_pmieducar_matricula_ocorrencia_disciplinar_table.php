<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarMatriculaOcorrenciaDisciplinarTable extends Migration
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
                CREATE SEQUENCE pmieducar.ocorrencia_disciplinar_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.matricula_ocorrencia_disciplinar (
                    ref_cod_matricula integer NOT NULL,
                    ref_cod_tipo_ocorrencia_disciplinar integer NOT NULL,
                    sequencial integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    observacao text NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    visivel_pais integer,
                    cod_ocorrencia_disciplinar integer DEFAULT nextval(\'pmieducar.ocorrencia_disciplinar_seq\'::regclass) NOT NULL,
	                updated_at timestamp NULL DEFAULT now()
                );

                ALTER TABLE ONLY pmieducar.matricula_ocorrencia_disciplinar
                    ADD CONSTRAINT matricula_ocorrencia_disciplinar_pkey PRIMARY KEY (ref_cod_matricula, ref_cod_tipo_ocorrencia_disciplinar, sequencial);

                SELECT pg_catalog.setval(\'pmieducar.ocorrencia_disciplinar_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.matricula_ocorrencia_disciplinar');

        DB::unprepared('DROP SEQUENCE pmieducar.ocorrencia_disciplinar_seq;');
    }
}
