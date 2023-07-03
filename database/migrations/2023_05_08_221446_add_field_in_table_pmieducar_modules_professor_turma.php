<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('modules.professor_turma', function (Blueprint $table) {
            $table->smallInteger('outras_unidades_curriculares_obrigatorias')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('modules.professor_turma', function (Blueprint $table) {
            $table->dropColumn('outras_unidades_curriculares_obrigatorias')->default(0);
        });
    }
};
