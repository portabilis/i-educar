<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsMaxMinAbesences extends Migration
{
    public function up()
    {
        Schema::table('modules.regra_avaliacao', function (Blueprint $table) {
            $table->integer('falta_minima_geral')->default(0);
            $table->integer('falta_maxima_geral')->default(100);
        });
    }

    public function down()
    {
        Schema::table('modules.regra_avaliacao', function (Blueprint $table) {
            $table->dropColumn('falta_minima_geral');
            $table->dropColumn('falta_maxima_geral');
        });
    }
}
