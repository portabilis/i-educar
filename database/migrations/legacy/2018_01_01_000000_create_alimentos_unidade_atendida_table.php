<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosUnidadeAtendidaTable extends Migration
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
                
                CREATE TABLE alimentos.unidade_atendida (
                    iduni integer DEFAULT nextval(\'alimentos.unidade_atendida_iduni_seq\'::regclass) NOT NULL,
                    idcad integer NOT NULL,
                    idtip integer NOT NULL,
                    codigo character varying(10) NOT NULL,
                    idcli character varying(10) NOT NULL,
                    nome character varying(40) NOT NULL,
                    endereco character varying(60) NOT NULL,
                    complemento character varying(30),
                    bairro character varying(30) NOT NULL,
                    cep character varying(8),
                    telefone character varying(11) NOT NULL,
                    fax character varying(11),
                    email character varying(40),
                    idpes integer NOT NULL,
                    diretor character varying(40) NOT NULL
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
        Schema::dropIfExists('alimentos.unidade_atendida');
    }
}
