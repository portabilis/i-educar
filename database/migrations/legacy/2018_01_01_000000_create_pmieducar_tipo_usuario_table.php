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
    }
}
