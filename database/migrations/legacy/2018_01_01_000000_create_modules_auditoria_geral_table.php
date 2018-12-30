<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesAuditoriaGeralTable extends Migration
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

                CREATE TABLE modules.auditoria_geral (
                    usuario_id integer,
                    operacao smallint,
                    rotina character varying(50),
                    valor_antigo json,
                    valor_novo json,
                    data_hora timestamp without time zone,
                    codigo character varying,
                    id integer NOT NULL,
                    query text
                );

                -- ALTER SEQUENCE modules.auditoria_geral_id_seq OWNED BY modules.auditoria_geral.id;
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
        Schema::dropIfExists('modules.auditoria_geral');
    }
}
