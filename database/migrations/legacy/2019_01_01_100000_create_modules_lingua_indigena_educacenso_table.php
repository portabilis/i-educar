<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesLinguaIndigenaEducacensoTable extends Migration
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
                
                CREATE TABLE modules.lingua_indigena_educacenso (
                    id integer NOT NULL,
                    lingua character varying(255)
                );
                
                ALTER TABLE ONLY modules.lingua_indigena_educacenso
                    ADD CONSTRAINT lingua_indigena_educacenso_pk PRIMARY KEY (id);
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
        Schema::dropIfExists('modules.lingua_indigena_educacenso');
    }
}
