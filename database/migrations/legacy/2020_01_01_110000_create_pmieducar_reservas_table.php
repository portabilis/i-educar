<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
                CREATE SEQUENCE pmieducar.reservas_cod_reserva_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

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

                ALTER TABLE ONLY pmieducar.reservas
                    ADD CONSTRAINT reservas_pkey PRIMARY KEY (cod_reserva);

                SELECT pg_catalog.setval(\'pmieducar.reservas_cod_reserva_seq\', 1, false);
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

        DB::unprepared('DROP SEQUENCE pmieducar.reservas_cod_reserva_seq;');
    }
}
