<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

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
                SET default_with_oids = false;

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
                    cod_dispensa integer DEFAULT nextval(\'pmieducar.dispensa_disciplina_cod_dispensa_seq\'::regclass) NOT NULL
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
        Schema::dropIfExists('pmieducar.dispensa_disciplina');
    }
}