<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarTipoUsuarioTable extends Migration
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
                
                CREATE SEQUENCE pmieducar.tipo_usuario_cod_tipo_usuario_seq
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.tipo_usuario (
                    cod_tipo_usuario integer DEFAULT nextval(\'pmieducar.tipo_usuario_cod_tipo_usuario_seq\'::regclass) NOT NULL,
                    ref_funcionario_cad integer NOT NULL,
                    ref_funcionario_exc integer,
                    nm_tipo character varying(255) NOT NULL,
                    descricao text,
                    nivel integer NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
                );
                
                ALTER TABLE ONLY pmieducar.tipo_usuario
                    ADD CONSTRAINT tipo_usuario_pkey PRIMARY KEY (cod_tipo_usuario);

                SELECT pg_catalog.setval(\'pmieducar.tipo_usuario_cod_tipo_usuario_seq\', 3, true);
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
        Schema::dropIfExists('pmieducar.tipo_usuario');

        DB::unprepared('DROP SEQUENCE pmieducar.tipo_usuario_cod_tipo_usuario_seq;');
    }
}
