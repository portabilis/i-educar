<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarEscolaUsuarioTable extends Migration
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
                CREATE SEQUENCE pmieducar.escola_usuario_id_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.escola_usuario (
                    id integer NOT NULL,
                    ref_cod_usuario integer NOT NULL,
                    ref_cod_escola integer NOT NULL,
                    escola_atual integer
                );

                ALTER SEQUENCE pmieducar.escola_usuario_id_seq OWNED BY pmieducar.escola_usuario.id;

                ALTER TABLE ONLY pmieducar.escola_usuario ALTER COLUMN id SET DEFAULT nextval(\'pmieducar.escola_usuario_id_seq\'::regclass);

                ALTER TABLE ONLY pmieducar.escola_usuario
                    ADD CONSTRAINT escola_usuario_pkey PRIMARY KEY (id);

                SELECT pg_catalog.setval(\'pmieducar.escola_usuario_id_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.escola_usuario');
    }
}
