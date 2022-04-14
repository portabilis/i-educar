<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarDispensaDisciplinaTable extends Migration
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
                CREATE SEQUENCE pmieducar.dispensa_disciplina_cod_dispensa_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.dispensa_disciplina (
                    ref_cod_matricula integer NOT NULL,
                    ref_cod_disciplina integer NOT NULL,
                    ref_cod_escola integer NOT NULL,
                    ref_cod_serie integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    ref_cod_tipo_dispensa integer NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    observacao text,
                    cod_dispensa integer DEFAULT nextval(\'pmieducar.dispensa_disciplina_cod_dispensa_seq\'::regclass) NOT NULL,
	                updated_at timestamp NULL DEFAULT now()
                );

                ALTER TABLE ONLY pmieducar.dispensa_disciplina
                    ADD CONSTRAINT cod_dispensa_pkey PRIMARY KEY (cod_dispensa);

                CREATE INDEX i_dispensa_disciplina_ref_cod_matricula ON pmieducar.dispensa_disciplina USING btree (ref_cod_matricula);

                SELECT pg_catalog.setval(\'pmieducar.dispensa_disciplina_cod_dispensa_seq\', 1, true);
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
        Schema::dropIfExists('pmieducar.dispensa_disciplina');

        DB::unprepared('DROP SEQUENCE pmieducar.dispensa_disciplina_cod_dispensa_seq;');
    }
}
