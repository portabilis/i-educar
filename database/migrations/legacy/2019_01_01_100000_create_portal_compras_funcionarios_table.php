<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalComprasFuncionariosTable extends Migration
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

                CREATE TABLE portal.compras_funcionarios (
                    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL
                );
                
                ALTER TABLE ONLY portal.compras_funcionarios
                    ADD CONSTRAINT compras_funcionarios_pk PRIMARY KEY (ref_ref_cod_pessoa_fj);
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
        Schema::dropIfExists('portal.compras_funcionarios');
    }
}
