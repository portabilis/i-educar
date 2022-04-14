<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarUsuarioTable extends Migration
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
                CREATE TABLE pmieducar.usuario (
                    cod_usuario integer NOT NULL,
                    ref_cod_instituicao integer,
                    ref_funcionario_cad integer NOT NULL,
                    ref_funcionario_exc integer,
                    ref_cod_tipo_usuario integer,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
                );

                ALTER TABLE ONLY pmieducar.usuario
                    ADD CONSTRAINT usuario_pkey PRIMARY KEY (cod_usuario);
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
        Schema::dropIfExists('pmieducar.usuario');
    }
}
