<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarTipoDispensaTable extends Migration
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
                
                CREATE SEQUENCE pmieducar.tipo_dispensa_cod_tipo_dispensa_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.tipo_dispensa (
                    cod_tipo_dispensa integer DEFAULT nextval(\'pmieducar.tipo_dispensa_cod_tipo_dispensa_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_tipo character varying(255) NOT NULL,
                    descricao text,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ref_cod_instituicao integer NOT NULL
                );
                
                ALTER TABLE ONLY pmieducar.tipo_dispensa
                    ADD CONSTRAINT tipo_dispensa_pkey PRIMARY KEY (cod_tipo_dispensa);

                SELECT pg_catalog.setval(\'pmieducar.tipo_dispensa_cod_tipo_dispensa_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.tipo_dispensa');

        DB::unprepared('DROP SEQUENCE pmieducar.tipo_dispensa_cod_tipo_dispensa_seq;');
    }
}
