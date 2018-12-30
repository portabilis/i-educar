<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateConsistenciacaoIncoerenciaEnderecoTable extends Migration
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
                
                CREATE TABLE consistenciacao.incoerencia_endereco (
                    id_inc_end integer DEFAULT nextval(\'consistenciacao.incoerencia_endereco_id_inc_end_seq\'::regclass) NOT NULL,
                    idinc integer NOT NULL,
                    tipo character varying(60) NOT NULL,
                    tipo_logradouro character varying(15),
                    logradouro character varying(150),
                    numero numeric(6,0),
                    letra character(1),
                    complemento character varying(20),
                    bairro character varying(40),
                    cep numeric(8,0),
                    cidade character varying(60),
                    uf character varying(30),
                    CONSTRAINT ck_incoerencia_endereco_tipo CHECK ((((tipo)::text >= ((1)::numeric)::text) AND ((tipo)::text <= ((3)::numeric)::text)))
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
        Schema::dropIfExists('consistenciacao.incoerencia_endereco');
    }
}
