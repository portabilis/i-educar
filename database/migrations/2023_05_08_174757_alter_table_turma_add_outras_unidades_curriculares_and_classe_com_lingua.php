<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('pmieducar.turma', function (Blueprint $table) {
            $table->text('outras_unidades_curriculares_obrigatorias')->nullable();
            $table->smallInteger('classe_com_lingua_brasileira_sinais')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pmieducar.turma', function (Blueprint $table) {
            $table->dropColumn('outras_unidades_curriculares_obrigatorias');
            $table->dropColumn('classe_com_lingua_brasileira_sinais');
        });
    }
};
