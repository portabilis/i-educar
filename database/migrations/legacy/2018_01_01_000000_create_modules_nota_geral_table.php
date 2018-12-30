<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesNotaGeralTable extends Migration
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
                SET default_with_oids = false;
                
                CREATE TABLE modules.nota_geral (
                    id integer DEFAULT nextval(\'modules.nota_geral_id_seq\'::regclass) NOT NULL,
                    nota_aluno_id integer NOT NULL,
                    nota numeric(8,4) DEFAULT 0,
                    nota_arredondada character varying(10) DEFAULT 0,
                    etapa character varying(2) NOT NULL
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
        Schema::dropIfExists('modules.nota_geral');
    }
}
