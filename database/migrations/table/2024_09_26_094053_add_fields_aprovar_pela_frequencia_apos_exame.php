<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('modules.regra_avaliacao', function (Blueprint $table) {
            $table->boolean('aprovar_pela_frequencia_apos_exame')->default(false);
        });
    }

    public function down()
    {
        Schema::table('modules.regra_avaliacao', function (Blueprint $table) {
            $table->dropColumn('aprovar_pela_frequencia_apos_exame');
        });
    }
};
