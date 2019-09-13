<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarReservaVagaTable extends Migration
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
                
                CREATE SEQUENCE pmieducar.reserva_vaga_cod_reserva_vaga_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.reserva_vaga (
                    cod_reserva_vaga integer DEFAULT nextval(\'pmieducar.reserva_vaga_cod_reserva_vaga_seq\'::regclass) NOT NULL,
                    ref_ref_cod_escola integer NOT NULL,
                    ref_ref_cod_serie integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    ref_cod_aluno integer,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    nm_aluno character varying(255),
                    cpf_responsavel numeric(11,0)
                );
                
                ALTER TABLE ONLY pmieducar.reserva_vaga
                    ADD CONSTRAINT reserva_vaga_pkey PRIMARY KEY (cod_reserva_vaga);

                SELECT pg_catalog.setval(\'pmieducar.reserva_vaga_cod_reserva_vaga_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.reserva_vaga');

        DB::unprepared('DROP SEQUENCE pmieducar.reserva_vaga_cod_reserva_vaga_seq;');
    }
}
