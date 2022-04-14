<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDescricaoOnCursoTable extends Migration
{
    public function up()
    {
        Schema::table('pmieducar.curso', function (Blueprint $table) {
            $table->string('descricao', 50)->nullable();
        });
    }

    public function down()
    {
        Schema::table('pmieducar.curso', function (Blueprint $table) {
            $table->dropColumn('descricao');
        });
    }
}
