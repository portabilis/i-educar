<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsPedagogicalPlannings2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.planejamento_aula', function (Blueprint $table) {
            $table->text('recursos_didaticos')->nullable();
            $table->text('registro_adaptacao')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.planejamento_aula', function (Blueprint $table) {
            $table->dropColumn('recursos_didaticos');
            $table->dropColumn('registro_adaptacao');
        });
    }
}
