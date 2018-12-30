<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarMaterialTipoTable extends Migration
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
                
                CREATE TABLE pmieducar.material_tipo (
                    cod_material_tipo integer DEFAULT nextval(\'pmieducar.material_tipo_cod_material_tipo_seq\'::regclass) NOT NULL,
                    ref_usuario_cad integer NOT NULL,
                    ref_usuario_exc integer,
                    nm_tipo character varying(255) NOT NULL,
                    desc_tipo text,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
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
        Schema::dropIfExists('pmieducar.material_tipo');
    }
}
