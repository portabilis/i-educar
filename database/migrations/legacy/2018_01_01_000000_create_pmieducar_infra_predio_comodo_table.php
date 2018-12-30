<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarInfraPredioComodoTable extends Migration
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
                
                CREATE TABLE pmieducar.infra_predio_comodo (
                    cod_infra_predio_comodo integer DEFAULT nextval(\'pmieducar.infra_predio_comodo_cod_infra_predio_comodo_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    ref_cod_infra_comodo_funcao integer NOT NULL,
                    ref_cod_infra_predio integer NOT NULL,
                    nm_comodo character varying(255) NOT NULL,
                    desc_comodo text,
                    area double precision NOT NULL,
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
        Schema::dropIfExists('pmieducar.infra_predio_comodo');
    }
}
