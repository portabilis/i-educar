<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmiotopicReuniaoTable extends Migration
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
                
                CREATE TABLE pmiotopic.reuniao (
                    cod_reuniao integer DEFAULT nextval(\'pmiotopic.reuniao_cod_reuniao_seq\'::regclass) NOT NULL,
                    ref_grupos_moderador integer NOT NULL,
                    ref_moderador integer NOT NULL,
                    data_inicio_marcado timestamp without time zone NOT NULL,
                    data_fim_marcado timestamp without time zone NOT NULL,
                    data_inicio_real timestamp without time zone,
                    data_fim_real timestamp without time zone,
                    descricao text NOT NULL,
                    email_enviado timestamp without time zone,
                    publica smallint DEFAULT 0 NOT NULL
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
        Schema::dropIfExists('pmiotopic.reuniao');
    }
}