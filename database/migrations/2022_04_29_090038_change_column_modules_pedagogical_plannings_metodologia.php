<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnModulesPedagogicalPlanningsMetodologia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.planejamento_aula', function (Blueprint $table) {
            $table->text('ddp')->change();
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
            $table->text('ddp')->nullable()->change();
        });
    }
}
