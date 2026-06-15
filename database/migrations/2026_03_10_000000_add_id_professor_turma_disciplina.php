<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('ALTER TABLE modules.professor_turma_disciplina DROP CONSTRAINT professor_turma_disciplina_pk');

        Schema::table('modules.professor_turma_disciplina', function (Blueprint $table) {
            $table->unique(['professor_turma_id', 'componente_curricular_id']);
        });

        Schema::table('modules.professor_turma_disciplina', function (Blueprint $table) {
            $table->id();
        });
    }

    public function down(): void
    {
        Schema::table('modules.professor_turma_disciplina', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('modules.professor_turma_disciplina', function (Blueprint $table) {
            $table->dropUnique(['professor_turma_id', 'componente_curricular_id']);
        });

        DB::unprepared('ALTER TABLE ONLY modules.professor_turma_disciplina ADD CONSTRAINT professor_turma_disciplina_pk PRIMARY KEY (professor_turma_id, componente_curricular_id);');
    }
};
