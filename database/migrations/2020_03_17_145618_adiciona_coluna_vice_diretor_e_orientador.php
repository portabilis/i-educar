<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdicionaColunaViceDiretorEOrientador extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.escola', function (Blueprint $table) {
            $table->integer('qtd_vice_diretor')->nullable();
            $table->integer('qtd_orientador_comunitario')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.escola', function (Blueprint $table) {
            $table->dropColumn('qtd_vice_diretor');
            $table->dropColumn('qtd_orientador_comunitario');
        });
    }
}
