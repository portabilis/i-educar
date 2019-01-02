<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosClienteTable extends Migration
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
                
                CREATE TABLE alimentos.cliente (
                    idcli character varying(10) NOT NULL,
                    nome character varying(40) NOT NULL,
                    cnpj character varying(14) NOT NULL,
                    endereco character varying(60) NOT NULL,
                    bairro character varying(30) NOT NULL,
                    cidade character varying(18) NOT NULL,
                    cep character varying(8),
                    uf character varying(2) NOT NULL,
                    telefone character varying(11) NOT NULL,
                    fax character varying(11),
                    email character varying(40),
                    prefeito character varying(40) NOT NULL,
                    educacao character varying(40) NOT NULL,
                    administracao character varying(40) NOT NULL,
                    coordenacao character varying(40) NOT NULL,
                    inscritos character(1) NOT NULL,
                    idpes integer NOT NULL,
                    identificacao character varying(20) NOT NULL,
                    tab_produtos character(1),
                    CONSTRAINT ck_tab_produtos CHECK (((tab_produtos = \'1\'::bpchar) OR (tab_produtos = \'2\'::bpchar)))
                );
                
                ALTER TABLE ONLY alimentos.cliente
                    ADD CONSTRAINT pk_cliente PRIMARY KEY (idcli);
                    
                CREATE UNIQUE INDEX un_cliente ON alimentos.cliente USING btree (idcli, identificacao);
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
        Schema::dropIfExists('alimentos.cliente');
    }
}
