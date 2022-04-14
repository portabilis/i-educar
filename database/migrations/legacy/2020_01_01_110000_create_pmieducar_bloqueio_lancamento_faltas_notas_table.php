<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarBloqueioLancamentoFaltasNotasTable extends Migration
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
                CREATE SEQUENCE public.bloqueio_lancamento_faltas_notas_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.bloqueio_lancamento_faltas_notas (
                    cod_bloqueio integer DEFAULT nextval(\'public.bloqueio_lancamento_faltas_notas_seq\'::regclass) NOT NULL,
                    ano integer NOT NULL,
                    ref_cod_escola integer NOT NULL,
                    etapa integer NOT NULL,
                    data_inicio date NOT NULL,
                    data_fim date NOT NULL
                );

                ALTER TABLE ONLY pmieducar.bloqueio_lancamento_faltas_notas
                    ADD CONSTRAINT fk_bloqueio_lancamento_faltas_notas PRIMARY KEY (cod_bloqueio);

                SELECT pg_catalog.setval(\'public.bloqueio_lancamento_faltas_notas_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.bloqueio_lancamento_faltas_notas');

        DB::unprepared('DROP SEQUENCE public.bloqueio_lancamento_faltas_notas_seq;');
    }
}
