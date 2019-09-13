<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarMotivoAfastamentoTable extends Migration
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
                
                CREATE SEQUENCE pmieducar.motivo_afastamento_cod_motivo_afastamento_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.motivo_afastamento (
                    cod_motivo_afastamento integer DEFAULT nextval(\'pmieducar.motivo_afastamento_cod_motivo_afastamento_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_motivo character varying(255) NOT NULL,
                    descricao text,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ref_cod_instituicao integer NOT NULL
                );
                
                ALTER TABLE ONLY pmieducar.motivo_afastamento
                    ADD CONSTRAINT motivo_afastamento_pkey PRIMARY KEY (cod_motivo_afastamento);

                SELECT pg_catalog.setval(\'pmieducar.motivo_afastamento_cod_motivo_afastamento_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.motivo_afastamento');

        DB::unprepared('DROP SEQUENCE pmieducar.motivo_afastamento_cod_motivo_afastamento_seq;');
    }
}
