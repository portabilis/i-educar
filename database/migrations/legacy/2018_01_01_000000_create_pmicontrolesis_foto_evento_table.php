<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmicontrolesisFotoEventoTable extends Migration
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
                
                CREATE TABLE pmicontrolesis.foto_evento (
                    cod_foto_evento integer DEFAULT nextval(\'pmicontrolesis.foto_evento_cod_foto_evento_seq\'::regclass) NOT NULL,
                    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
                    data_foto timestamp without time zone,
                    titulo character varying(255),
                    descricao text,
                    caminho character varying(255),
                    altura integer,
                    largura integer,
                    nm_credito character varying(255)
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
        Schema::dropIfExists('pmicontrolesis.foto_evento');
    }
}
