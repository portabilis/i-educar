<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosFornecedorUnidadeAtendidaTable extends Migration
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
                
                CREATE TABLE alimentos.fornecedor_unidade_atendida (
                    iduni integer NOT NULL,
                    idfor integer NOT NULL
                );
                
                CREATE UNIQUE INDEX un_fornecedor_unidade_atend ON alimentos.fornecedor_unidade_atendida USING btree (iduni, idfor);
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
        Schema::dropIfExists('alimentos.fornecedor_unidade_atendida');
    }
}
