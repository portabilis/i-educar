<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarMotivoSuspensaoTable extends Migration
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
                
                CREATE SEQUENCE pmieducar.motivo_suspensao_cod_motivo_suspensao_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.motivo_suspensao (
                    cod_motivo_suspensao integer DEFAULT nextval(\'pmieducar.motivo_suspensao_cod_motivo_suspensao_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_motivo character varying(255) NOT NULL,
                    descricao text,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ref_cod_biblioteca integer
                );
                
                ALTER TABLE ONLY pmieducar.motivo_suspensao
                    ADD CONSTRAINT motivo_suspensao_pkey PRIMARY KEY (cod_motivo_suspensao);

                SELECT pg_catalog.setval(\'pmieducar.motivo_suspensao_cod_motivo_suspensao_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.motivo_suspensao');

        DB::unprepared('DROP SEQUENCE pmieducar.motivo_suspensao_cod_motivo_suspensao_seq;');
    }
}
