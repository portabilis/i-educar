<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDesconsiderarLancamentoFrequencia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.regra_avaliacao', function (Blueprint $table) {
            $table->boolean('desconsiderar_lancamento_frequencia')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.regra_avaliacao', function (Blueprint $table) {
            $table->dropColumn('desconsiderar_lancamento_frequencia');
        });
    }
}
