<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('modules.professor_turma', function (Blueprint $table) {
            $table->smallInteger('unidades_curriculares_leciona')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('modules.professor_turma', function (Blueprint $table) {
            $table->dropColumn('unidades_curriculares_leciona')->default(0);
        });
    }
};
