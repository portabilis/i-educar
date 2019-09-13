<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarClienteSuspensaoTable extends Migration
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
                
                CREATE TABLE pmieducar.cliente_suspensao (
                    sequencial integer NOT NULL,
                    ref_cod_cliente integer NOT NULL,
                    ref_cod_motivo_suspensao integer NOT NULL,
                    ref_usuario_libera integer,
                    ref_usuario_suspende integer NOT NULL,
                    dias integer NOT NULL,
                    data_suspensao timestamp without time zone NOT NULL,
                    data_liberacao timestamp without time zone
                );
                
                ALTER TABLE ONLY pmieducar.cliente_suspensao
                    ADD CONSTRAINT cliente_suspensao_pkey PRIMARY KEY (sequencial, ref_cod_cliente, ref_cod_motivo_suspensao);
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
        Schema::dropIfExists('pmieducar.cliente_suspensao');
    }
}
