<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalAgendaResponsavelTable extends Migration
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

                CREATE TABLE portal.agenda_responsavel (
                    ref_cod_agenda integer NOT NULL,
                    ref_ref_cod_pessoa_fj integer NOT NULL,
                    principal smallint
                );
                
                ALTER TABLE ONLY portal.agenda_responsavel
                    ADD CONSTRAINT agenda_responsavel_pkey PRIMARY KEY (ref_cod_agenda, ref_ref_cod_pessoa_fj);
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
        Schema::dropIfExists('portal.agenda_responsavel');
    }
}
