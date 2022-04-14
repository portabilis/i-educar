<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModalidadeEnsinoInMatricula extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('matricula', function (Blueprint $table) {
            $table->smallInteger('modalidade_ensino')->default(3);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('matricula', function (Blueprint $table) {
            $table->dropColumn('modalidade_ensino');
        });
    }
}
