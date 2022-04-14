<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateModulesMediaGeralTable extends Migration
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
                CREATE TABLE modules.media_geral (
                    nota_aluno_id integer NOT NULL,
                    media numeric(8,4) DEFAULT 0,
                    media_arredondada character varying(10) DEFAULT 0,
                    etapa character varying(2) NOT NULL
                );

                ALTER TABLE ONLY modules.media_geral
                    ADD CONSTRAINT media_geral_pkey PRIMARY KEY (nota_aluno_id, etapa);
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
        Schema::dropIfExists('modules.media_geral');
    }
}
