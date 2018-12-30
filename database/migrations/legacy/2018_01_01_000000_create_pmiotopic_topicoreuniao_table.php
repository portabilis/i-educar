<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmiotopicTopicoreuniaoTable extends Migration
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
                
                CREATE TABLE pmiotopic.topicoreuniao (
                    ref_cod_topico integer NOT NULL,
                    ref_cod_reuniao integer NOT NULL,
                    parecer text,
                    finalizado smallint,
                    data_parecer timestamp without time zone
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
        Schema::dropIfExists('pmiotopic.topicoreuniao');
    }
}
