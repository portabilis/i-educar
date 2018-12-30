<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarAuditoriaFaltaComponenteDispensaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        # FIXME

        DB::unprepared(
            '
                SET default_with_oids = false;

                CREATE TABLE pmieducar.auditoria_falta_componente_dispensa (
                    id integer NOT NULL,
                    ref_cod_matricula integer NOT NULL,
                    ref_cod_componente_curricular integer NOT NULL,
                    quantidade integer NOT NULL,
                    etapa integer NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL
                );

                -- ALTER SEQUENCE pmieducar.auditoria_falta_componente_dispensa_id_seq OWNED BY pmieducar.auditoria_falta_componente_dispensa.id;
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
        Schema::dropIfExists('pmieducar.auditoria_falta_componente_dispensa');
    }
}
