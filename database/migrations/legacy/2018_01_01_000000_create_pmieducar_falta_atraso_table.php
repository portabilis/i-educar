<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarFaltaAtrasoTable extends Migration
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
                
                CREATE TABLE pmieducar.falta_atraso (
                    cod_falta_atraso integer DEFAULT nextval(\'pmieducar.falta_atraso_cod_falta_atraso_seq\'::regclass) NOT NULL,
                    ref_cod_escola integer NOT NULL,
                    ref_ref_cod_instituicao integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    ref_cod_servidor integer NOT NULL,
                    tipo smallint NOT NULL,
                    data_falta_atraso timestamp without time zone NOT NULL,
                    qtd_horas integer,
                    qtd_min integer,
                    justificada smallint DEFAULT (0)::smallint NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
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
        Schema::dropIfExists('pmieducar.falta_atraso');
    }
}
