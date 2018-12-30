<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarReservasTable extends Migration
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
                
                CREATE TABLE pmieducar.reservas (
                    cod_reserva integer DEFAULT nextval(\'pmieducar.reservas_cod_reserva_seq\'::regclass) NOT NULL,
                    ref_usuario_libera integer,
                    ref_usuario_cad integer NOT NULL,
                    ref_cod_cliente integer NOT NULL,
                    data_reserva timestamp without time zone,
                    data_prevista_disponivel timestamp without time zone,
                    data_retirada timestamp without time zone,
                    ref_cod_exemplar integer NOT NULL,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
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
        Schema::dropIfExists('pmieducar.reservas');
    }
}
