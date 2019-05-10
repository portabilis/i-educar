<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateRecebeEscolarizacaoEmOutroEspacoTo2019V6 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(' UPDATE pmieducar.aluno
            SET recebe_escolarizacao_em_outro_espaco =
                CASE recebe_escolarizacao_em_outro_espaco
                WHEN 2 THEN 3
                WHEN 3 THEN 2
                ELSE 1
            END
            WHERE recebe_escolarizacao_em_outro_espaco IS NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement(' UPDATE pmieducar.aluno
            SET recebe_escolarizacao_em_outro_espaco =
                CASE recebe_escolarizacao_em_outro_espaco
                WHEN 2 THEN 3
                WHEN 3 THEN 2
                ELSE 1
            END
            WHERE recebe_escolarizacao_em_outro_espaco IS NOT NULL
        ');
    }
}
