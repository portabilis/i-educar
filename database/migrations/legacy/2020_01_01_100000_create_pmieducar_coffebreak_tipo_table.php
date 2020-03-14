<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarCoffebreakTipoTable extends Migration
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
                
                CREATE SEQUENCE pmieducar.coffebreak_tipo_cod_coffebreak_tipo_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.coffebreak_tipo (
                    cod_coffebreak_tipo integer DEFAULT nextval(\'pmieducar.coffebreak_tipo_cod_coffebreak_tipo_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_tipo character varying(255) NOT NULL,
                    desc_tipo text,
                    custo_unitario double precision NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
                );
                
                ALTER TABLE ONLY pmieducar.coffebreak_tipo
                    ADD CONSTRAINT coffebreak_tipo_pkey PRIMARY KEY (cod_coffebreak_tipo);

                CREATE INDEX i_coffebreak_tipo_ativo ON pmieducar.coffebreak_tipo USING btree (ativo);

                CREATE INDEX i_coffebreak_tipo_custo_unitario ON pmieducar.coffebreak_tipo USING btree (custo_unitario);

                CREATE INDEX i_coffebreak_tipo_nm_tipo ON pmieducar.coffebreak_tipo USING btree (nm_tipo);

                CREATE INDEX i_coffebreak_tipo_ref_usuario_cad ON pmieducar.coffebreak_tipo USING btree (ref_usuario_cad);

                SELECT pg_catalog.setval(\'pmieducar.coffebreak_tipo_cod_coffebreak_tipo_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.coffebreak_tipo');

        DB::unprepared('DROP SEQUENCE pmieducar.coffebreak_tipo_cod_coffebreak_tipo_seq;');
    }
}
