<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarEscolaCursoTable extends Migration
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
                CREATE TABLE pmieducar.escola_curso (
                    ref_cod_escola integer NOT NULL,
                    ref_cod_curso integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    autorizacao character varying(255),
                    anos_letivos smallint[] DEFAULT \'{}\'::smallint[] NOT NULL,
	                updated_at timestamp NULL DEFAULT now()
                );

                ALTER TABLE ONLY pmieducar.escola_curso
                    ADD CONSTRAINT escola_curso_pkey PRIMARY KEY (ref_cod_escola, ref_cod_curso);

                CREATE INDEX i_escola_curso_ativo ON pmieducar.escola_curso USING btree (ativo);

                CREATE INDEX i_escola_curso_ref_usuario_cad ON pmieducar.escola_curso USING btree (ref_usuario_cad);
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
        Schema::dropIfExists('pmieducar.escola_curso');
    }
}
