<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosTipoRefeicaoTable extends Migration
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
                
                CREATE TABLE alimentos.tipo_refeicao (
                    idtre integer DEFAULT nextval(\'alimentos.tipo_refeicao_idtre_seq\'::regclass) NOT NULL,
                    idcli character varying(10) NOT NULL,
                    descricao character varying(30) NOT NULL
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
        Schema::dropIfExists('alimentos.tipo_refeicao');
    }
}
