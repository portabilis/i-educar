<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateModulesEtapasEducacensoTable extends Migration
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
                CREATE TABLE modules.etapas_educacenso (
                    id integer NOT NULL,
                    nome character varying(255)
                );

                ALTER TABLE ONLY modules.etapas_educacenso
                    ADD CONSTRAINT etapas_educacenso_pk PRIMARY KEY (id);
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
        Schema::dropIfExists('modules.etapas_educacenso');
    }
}
