<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBatchColumnInExemption extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.dispensa_disciplina', function (Blueprint $table) {
            $table->boolean('batch')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.dispensa_disciplina', function (Blueprint $table) {
            $table->dropColumn('batch');
        });
    }
}
