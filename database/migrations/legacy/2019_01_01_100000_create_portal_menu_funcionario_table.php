<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalMenuFuncionarioTable extends Migration
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

                CREATE TABLE portal.menu_funcionario (
                    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
                    cadastra smallint DEFAULT (0)::smallint NOT NULL,
                    exclui smallint DEFAULT (0)::smallint NOT NULL,
                    ref_cod_menu_submenu integer DEFAULT 0 NOT NULL
                );
                
                ALTER TABLE ONLY portal.menu_funcionario
                    ADD CONSTRAINT menu_funcionario_pk PRIMARY KEY (ref_ref_cod_pessoa_fj, ref_cod_menu_submenu);
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
        Schema::dropIfExists('portal.menu_funcionario');
    }
}
