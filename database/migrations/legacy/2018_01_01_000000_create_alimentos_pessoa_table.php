<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosPessoaTable extends Migration
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
                
                CREATE TABLE alimentos.pessoa (
                    idpes integer DEFAULT nextval(\'alimentos.pessoa_idpes_seq\'::regclass) NOT NULL,
                    tipo character varying(1) NOT NULL,
                    CONSTRAINT ck_pessoa CHECK ((((tipo)::text = \'C\'::text) OR ((tipo)::text = \'F\'::text) OR ((tipo)::text = \'U\'::text)))
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
        Schema::dropIfExists('alimentos.pessoa');
    }
}
