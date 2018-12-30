<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarMaterialDidaticoTable extends Migration
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
                
                CREATE SEQUENCE pmieducar.material_didatico_cod_material_didatico_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;
                    
                CREATE TABLE pmieducar.material_didatico (
                    cod_material_didatico integer DEFAULT nextval(\'pmieducar.material_didatico_cod_material_didatico_seq\'::regclass) NOT NULL,
                    ref_cod_instituicao integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    ref_cod_material_tipo integer NOT NULL,
                    nm_material character varying(255) NOT NULL,
                    desc_material text,
                    custo_unitario double precision NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
                );
                
                ALTER TABLE ONLY pmieducar.material_didatico
                    ADD CONSTRAINT material_didatico_pkey PRIMARY KEY (cod_material_didatico);

                SELECT pg_catalog.setval(\'pmieducar.material_didatico_cod_material_didatico_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.material_didatico');

        DB::unprepared('DROP SEQUENCE pmieducar.material_didatico_cod_material_didatico_seq;');
    }
}
