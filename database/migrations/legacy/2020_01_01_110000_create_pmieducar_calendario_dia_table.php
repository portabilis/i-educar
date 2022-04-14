<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarCalendarioDiaTable extends Migration
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
                CREATE TABLE pmieducar.calendario_dia (
                    ref_cod_calendario_ano_letivo integer NOT NULL,
                    mes integer NOT NULL,
                    dia integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    ref_cod_calendario_dia_motivo integer,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    descricao text
                );

                ALTER TABLE ONLY pmieducar.calendario_dia
                    ADD CONSTRAINT calendario_dia_pkey PRIMARY KEY (ref_cod_calendario_ano_letivo, mes, dia);

                CREATE INDEX i_calendario_dia_ativo ON pmieducar.calendario_dia USING btree (ativo);

                CREATE INDEX i_calendario_dia_dia ON pmieducar.calendario_dia USING btree (dia);

                CREATE INDEX i_calendario_dia_mes ON pmieducar.calendario_dia USING btree (mes);

                CREATE INDEX i_calendario_dia_ref_cod_calendario_dia_motivo ON pmieducar.calendario_dia USING btree (ref_cod_calendario_dia_motivo);

                CREATE INDEX i_calendario_dia_ref_usuario_cad ON pmieducar.calendario_dia USING btree (ref_usuario_cad);
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
        Schema::dropIfExists('pmieducar.calendario_dia');
    }
}
