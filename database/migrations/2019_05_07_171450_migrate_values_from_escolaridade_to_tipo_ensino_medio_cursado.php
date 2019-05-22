<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateValuesFromEscolaridadeToTipoEnsinoMedioCursado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            UPDATE pmieducar.servidor
            set tipo_ensino_medio_cursado = CASE WHEN escolaridade.escolaridade = 3 then 2 else 4 end
            from cadastro.escolaridade
            where servidor.ref_idesco = escolaridade.idesco
            and escolaridade.escolaridade IN (3,4)

        ');
    }
}
