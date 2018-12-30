<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

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
                SET default_with_oids = true;
                
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
    }
}
