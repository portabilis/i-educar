<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesMoradiaAlunoTable extends Migration
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

                CREATE TABLE modules.moradia_aluno (
                    ref_cod_aluno integer NOT NULL,
                    moradia character(1),
                    material character(1) DEFAULT \'A\'::bpchar,
                    casa_outra character varying(20),
                    moradia_situacao integer,
                    quartos integer,
                    sala integer,
                    copa integer,
                    banheiro integer,
                    garagem integer,
                    empregada_domestica character(1),
                    automovel character(1),
                    motocicleta character(1),
                    computador character(1),
                    geladeira character(1),
                    fogao character(1),
                    maquina_lavar character(1),
                    microondas character(1),
                    video_dvd character(1),
                    televisao character(1),
                    celular character(1),
                    telefone character(1),
                    quant_pessoas integer,
                    renda double precision,
                    agua_encanada character(1),
                    poco character(1),
                    energia character(1),
                    esgoto character(1),
                    fossa character(1),
                    lixo character(1)
                );
                
                ALTER TABLE ONLY modules.moradia_aluno
                    ADD CONSTRAINT moradia_aluno_pkei PRIMARY KEY (ref_cod_aluno);
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
        Schema::dropIfExists('modules.moradia_aluno');
    }
}
