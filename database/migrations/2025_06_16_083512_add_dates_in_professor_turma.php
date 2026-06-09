<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules.professor_turma', function (Blueprint $table) {
            $table->date('data_inicial')->nullable();
            $table->date('data_fim')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('modules.professor_turma', function (Blueprint $table) {
            $table->dropColumn('data_inicial');
            $table->dropColumn('data_fim');
        });
    }
};
