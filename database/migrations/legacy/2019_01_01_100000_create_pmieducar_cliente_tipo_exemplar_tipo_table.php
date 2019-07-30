<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarClienteTipoExemplarTipoTable extends Migration
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
                
                CREATE TABLE pmieducar.cliente_tipo_exemplar_tipo (
                    ref_cod_cliente_tipo integer NOT NULL,
                    ref_cod_exemplar_tipo integer NOT NULL,
                    dias_emprestimo numeric(3,0)
                );
                
                ALTER TABLE ONLY pmieducar.cliente_tipo_exemplar_tipo
                    ADD CONSTRAINT cliente_tipo_exemplar_tipo_pkey PRIMARY KEY (ref_cod_cliente_tipo, ref_cod_exemplar_tipo);
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
        Schema::dropIfExists('pmieducar.cliente_tipo_exemplar_tipo');
    }
}
