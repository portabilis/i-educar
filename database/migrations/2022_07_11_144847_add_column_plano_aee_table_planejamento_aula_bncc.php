<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPlanoAeeTablePlanejamentoAulaBncc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.planejamento_aula_bncc', function (Blueprint $table) {
            $table->char('plano_aee', 1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.planejamento_aula_bncc', function (Blueprint $table) {
            $table->dropColumn('plano_aee');
        });
    }
}
