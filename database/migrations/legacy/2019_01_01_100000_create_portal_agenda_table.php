<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalAgendaTable extends Migration
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
                
                CREATE SEQUENCE portal.agenda_cod_agenda_seq
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE portal.agenda (
                    cod_agenda integer DEFAULT nextval(\'portal.agenda_cod_agenda_seq\'::regclass) NOT NULL,
                    ref_ref_cod_pessoa_exc integer,
                    ref_ref_cod_pessoa_cad integer NOT NULL,
                    nm_agenda character varying NOT NULL,
                    publica smallint DEFAULT 0 NOT NULL,
                    envia_alerta smallint DEFAULT 0 NOT NULL,
                    data_cad timestamp without time zone NOT NULL,
                    data_edicao timestamp without time zone,
                    ref_ref_cod_pessoa_own integer
                );
                
                ALTER TABLE ONLY portal.agenda
                    ADD CONSTRAINT agenda_pkey PRIMARY KEY (cod_agenda);

                SELECT pg_catalog.setval(\'portal.agenda_cod_agenda_seq\', 1, true);
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
        Schema::dropIfExists('portal.agenda');

        DB::unprepared('DROP SEQUENCE portal.agenda_cod_agenda_seq;');
    }
}
