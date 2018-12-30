<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateSerieciascEscolaRegulamentacaoTable extends Migration
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
                
                CREATE TABLE serieciasc.escola_regulamentacao (
                    ref_cod_escola integer NOT NULL,
                    regulamentacao integer DEFAULT 1 NOT NULL,
                    situacao integer DEFAULT 1 NOT NULL,
                    data_criacao date,
                    ato_criacao integer DEFAULT 0,
                    numero_ato_criacao character varying(20),
                    data_ato_criacao date,
                    ato_paralizacao integer DEFAULT 0,
                    numero_ato_paralizacao character varying(20),
                    data_ato_paralizacao date,
                    data_extincao date,
                    ato_extincao integer DEFAULT 0,
                    numero_ato_extincao character varying(20),
                    data_ato_extincao date,
                    created_at timestamp without time zone NOT NULL,
                    updated_at timestamp without time zone
                );
                
                ALTER TABLE ONLY serieciasc.escola_regulamentacao
                    ADD CONSTRAINT educacenso_ref_cod_escola_pk PRIMARY KEY (ref_cod_escola);
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
        Schema::dropIfExists('serieciasc.escola_regulamentacao');
    }
}
