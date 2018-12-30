<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmiotopicParticipanteTable extends Migration
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
                
                CREATE TABLE pmiotopic.participante (
                    sequencial integer NOT NULL,
                    ref_ref_cod_grupos integer NOT NULL,
                    ref_ref_idpes integer NOT NULL,
                    ref_cod_reuniao integer NOT NULL,
                    data_chegada timestamp without time zone NOT NULL,
                    data_saida timestamp without time zone
                );
                
                ALTER TABLE ONLY pmiotopic.participante
                    ADD CONSTRAINT participante_pkey PRIMARY KEY (sequencial, ref_ref_cod_grupos, ref_ref_idpes, ref_cod_reuniao);
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
        Schema::dropIfExists('pmiotopic.participante');
    }
}
