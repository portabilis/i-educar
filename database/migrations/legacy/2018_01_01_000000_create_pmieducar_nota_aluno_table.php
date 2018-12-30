<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarNotaAlunoTable extends Migration
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
                SET default_with_oids = false;

                CREATE TABLE pmieducar.nota_aluno (
                    cod_nota_aluno integer DEFAULT nextval(\'pmieducar.nota_aluno_cod_nota_aluno_seq\'::regclass) NOT NULL,
                    ref_cod_disciplina integer,
                    ref_cod_escola integer,
                    ref_cod_serie integer,
                    ref_cod_matricula integer NOT NULL,
                    ref_sequencial integer,
                    ref_ref_cod_tipo_avaliacao integer,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    modulo smallint NOT NULL,
                    ref_cod_curso_disciplina integer,
                    nota double precision
                );
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
        Schema::dropIfExists('pmieducar.nota_aluno');
    }
}
