<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveValoresInvalidosDaLocalizacaoDiferenciada extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            UPDATE pmieducar.escola
            SET localizacao_diferenciada = null
            WHERE localizacao_diferenciada IN (4,5,6);
        ');
    }
}
