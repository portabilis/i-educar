<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class PopulaColunaDeRecursosTecnologicosParaAluno extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            UPDATE modules.moradia_aluno
            SET recursos_tecnologicos = (
                CASE
                    WHEN computador = 'S' AND celular = 'S'
                        THEN '[\"Computador\", \"Smartphone\"]'::json
                    WHEN celular = 'S'
                        THEN '[\"Smartphone\"]'::json
                    WHEN computador = 'S'
                        THEN '[\"Computador\"]'::json
                END
            )
            WHERE computador = 'S' OR celular = 'S';
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("
            UPDATE modules.moradia_aluno
            SET recursos_tecnologicos = null;
        ");
    }
}
