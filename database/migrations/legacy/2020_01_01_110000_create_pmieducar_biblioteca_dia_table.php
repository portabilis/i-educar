<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
