<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MigraCursosDeEducacaoProfissional extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            UPDATE pmieducar.turma
            SET cod_curso_profissional = (
                CASE cod_curso_profissional
                    WHEN 3057 THEN 11178
                    WHEN 9122 THEN 12186
                    WHEN 11161 THEN 3064
                END
            )
            WHERE cod_curso_profissional IN (3057, 9122, 11161);
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
            UPDATE pmieducar.turma
            SET cod_curso_profissional = (
                CASE cod_curso_profissional
                    WHEN 11178 THEN 3057
                    WHEN 12186 THEN 9122
                    WHEN 3064 THEN 11161
                END
            )
            WHERE cod_curso_profissional IN (11178, 12186, 3064);
        ');
    }
}
