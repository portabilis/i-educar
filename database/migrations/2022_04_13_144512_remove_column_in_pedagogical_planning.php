<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveColumnInPedagogicalPlanning extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.planejamento_aula', function (Blueprint $table) {
            $table->dropColumn(['ref_componente_curricular']);
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
            $table->integer('ref_componente_curricular')->nullable();
        });
    }
}