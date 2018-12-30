<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarAuditoriaNotaDispensaTable extends Migration
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

                CREATE TABLE pmieducar.auditoria_nota_dispensa (
                    id integer NOT NULL,
                    ref_cod_matricula integer NOT NULL,
                    ref_cod_componente_curricular integer NOT NULL,
                    nota numeric(8,4) NOT NULL,
                    etapa integer NOT NULL,
                    nota_recuperacao character varying(10),
                    nota_recuperacao_especifica character varying(10),
                    data_cadastro timestamp without time zone NOT NULL
                );

                -- ALTER SEQUENCE pmieducar.auditoria_nota_dispensa_id_seq OWNED BY pmieducar.auditoria_nota_dispensa.id;
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
        Schema::dropIfExists('pmieducar.auditoria_nota_dispensa');
    }
}
