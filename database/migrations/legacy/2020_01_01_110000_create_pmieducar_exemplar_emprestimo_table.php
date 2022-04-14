<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarExemplarEmprestimoTable extends Migration
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
                CREATE SEQUENCE pmieducar.exemplar_emprestimo_cod_emprestimo_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.exemplar_emprestimo (
                    cod_emprestimo integer DEFAULT nextval(\'pmieducar.exemplar_emprestimo_cod_emprestimo_seq\'::regclass) NOT NULL,
                    ref_usuario_devolucao integer,
                    ref_usuario_cad integer NOT NULL,
                    ref_cod_cliente integer NOT NULL,
                    ref_cod_exemplar integer NOT NULL,
                    data_retirada timestamp without time zone NOT NULL,
                    data_devolucao timestamp without time zone,
                    valor_multa double precision
                );

                ALTER TABLE ONLY pmieducar.exemplar_emprestimo
                    ADD CONSTRAINT exemplar_emprestimo_pkey PRIMARY KEY (cod_emprestimo);

                SELECT pg_catalog.setval(\'pmieducar.exemplar_emprestimo_cod_emprestimo_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.exemplar_emprestimo');

        DB::unprepared('DROP SEQUENCE pmieducar.exemplar_emprestimo_cod_emprestimo_seq;');
    }
}
