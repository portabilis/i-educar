<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarBibliotecaDiaTable extends Migration
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
                
                CREATE TABLE pmieducar.biblioteca_dia (
                    ref_cod_biblioteca integer NOT NULL,
                    dia numeric(1,0) NOT NULL
                );
                
                ALTER TABLE ONLY pmieducar.biblioteca_dia
                    ADD CONSTRAINT biblioteca_dia_pkey PRIMARY KEY (ref_cod_biblioteca, dia);
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
        Schema::dropIfExists('pmieducar.biblioteca_dia');
    }
}
