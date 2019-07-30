<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarCandidatoReservaVagaTable extends Migration
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
                
                CREATE SEQUENCE pmieducar.candidato_reserva_vaga_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.candidato_reserva_vaga (
                    cod_candidato_reserva_vaga integer DEFAULT nextval(\'pmieducar.candidato_reserva_vaga_seq\'::regclass) NOT NULL,
                    ano_letivo integer NOT NULL,
                    data_solicitacao date NOT NULL,
                    ref_cod_aluno integer NOT NULL,
                    ref_cod_serie integer NOT NULL,
                    ref_cod_turno integer,
                    ref_cod_pessoa_cad integer NOT NULL,
                    data_cad timestamp without time zone DEFAULT now() NOT NULL,
                    data_update timestamp without time zone DEFAULT now() NOT NULL,
                    ref_cod_matricula integer,
                    situacao character(1),
                    data_situacao date,
                    motivo character varying(255),
                    ref_cod_escola integer,
                    quantidade_membros smallint,
                    mae_fez_pre_natal boolean DEFAULT false,
                    membros_trabalham smallint,
                    hora_solicitacao time without time zone
                );
                
                ALTER TABLE ONLY pmieducar.candidato_reserva_vaga
                    ADD CONSTRAINT cod_candidato_reserva_vaga_pkey PRIMARY KEY (cod_candidato_reserva_vaga);

                SELECT pg_catalog.setval(\'pmieducar.candidato_reserva_vaga_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.candidato_reserva_vaga');

        DB::unprepared('DROP SEQUENCE pmieducar.candidato_reserva_vaga_seq;');
    }
}
