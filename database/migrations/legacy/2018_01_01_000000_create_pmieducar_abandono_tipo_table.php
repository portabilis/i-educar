<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarAbandonoTipoTable extends Migration
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

                CREATE SEQUENCE pmieducar.abandono_tipo_cod_abandono_tipo_seq
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.abandono_tipo (
                    cod_abandono_tipo integer DEFAULT nextval(\'pmieducar.abandono_tipo_cod_abandono_tipo_seq\'::regclass) NOT NULL,
                    ref_cod_instituicao integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer,
                    nome character varying(255) NOT NULL,
                    data_cadastro timestamp without time zone,
                    data_exclusao timestamp without time zone,
                    ativo integer
                );
                
                SELECT pg_catalog.setval(\'pmieducar.abandono_tipo_cod_abandono_tipo_seq\', 2, true);
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
        Schema::dropIfExists('pmieducar.abandono_tipo');
    }
}
