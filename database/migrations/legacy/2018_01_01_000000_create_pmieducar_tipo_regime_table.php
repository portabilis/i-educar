<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarTipoRegimeTable extends Migration
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
                
                CREATE TABLE pmieducar.tipo_regime (
                    cod_tipo_regime integer DEFAULT nextval(\'pmieducar.tipo_regime_cod_tipo_regime_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_tipo character varying(255) NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint NOT NULL,
                    ref_cod_instituicao integer NOT NULL
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
        Schema::dropIfExists('pmieducar.tipo_regime');
    }
}
