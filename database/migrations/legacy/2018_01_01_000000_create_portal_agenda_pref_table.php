<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalAgendaPrefTable extends Migration
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

                CREATE TABLE portal.agenda_pref (
                    cod_comp integer DEFAULT nextval(\'portal.agenda_pref_cod_comp_seq\'::regclass) NOT NULL,
                    data_comp date NOT NULL,
                    hora_comp time without time zone NOT NULL,
                    hora_f_comp time without time zone NOT NULL,
                    comp_comp text NOT NULL,
                    local_comp character(1) DEFAULT \'I\'::bpchar NOT NULL,
                    publico_comp character(1) DEFAULT \'S\'::bpchar NOT NULL,
                    agenda_de character(1) DEFAULT \'P\'::bpchar,
                    ref_cad integer,
                    versao integer DEFAULT 1 NOT NULL,
                    ref_auto_cod integer
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
        Schema::dropIfExists('portal.agenda_pref');
    }
}
