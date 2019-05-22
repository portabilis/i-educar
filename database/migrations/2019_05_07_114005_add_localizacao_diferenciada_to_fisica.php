<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocalizacaoDiferenciadaToFisica extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cadastro.fisica', function (Blueprint $table) {
            $table->integer('localizacao_diferenciada')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cadastro.fisica', function (Blueprint $table) {
            $table->dropColumn('localizacao_diferenciada');
        });
    }
}
