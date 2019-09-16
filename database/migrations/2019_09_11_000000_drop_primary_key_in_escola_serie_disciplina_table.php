<?php

use App\Support\Database\DropPrimaryKey;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class DropPrimaryKeyInEscolaSerieDisciplinaTable extends Migration
{
    use DropPrimaryKey;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropPrimaryKeysFromTable('escola_serie_disciplina');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared(
            '
                ALTER TABLE ONLY pmieducar.escola_serie_disciplina
                    ADD CONSTRAINT escola_serie_disciplina_pkey PRIMARY KEY (ref_ref_cod_serie, ref_ref_cod_escola, ref_cod_disciplina);
            '
        );
    }
}
