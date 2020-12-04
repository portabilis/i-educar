<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class InverteValoresDoTipoDeNotaDeComponentesDaSerie extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            UPDATE modules.componente_curricular_ano_escolar
            SET tipo_nota = (
                CASE tipo_nota
                    WHEN 1 THEN 2
                    WHEN 2 THEN 1
                END
            )
            WHERE tipo_nota IN (1,2);
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('
            UPDATE modules.componente_curricular_ano_escolar
            SET tipo_nota = (
                CASE tipo_nota
                    WHEN 1 THEN 2
                    WHEN 2 THEN 1
                END
            )
            WHERE tipo_nota IN (1,2);
        ');
    }
}
