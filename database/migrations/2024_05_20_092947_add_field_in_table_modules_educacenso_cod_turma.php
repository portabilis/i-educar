<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules.educacenso_cod_turma', function (Blueprint $table) {
            $table->smallInteger('turma_turno_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('modules.educacenso_cod_turma', function (Blueprint $table) {
            $table->dropColumn('turma_turno_id');
        });
    }
};
