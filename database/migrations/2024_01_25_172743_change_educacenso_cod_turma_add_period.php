<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        DB::unprepared('ALTER TABLE IF EXISTS modules.educacenso_cod_turma DROP CONSTRAINT IF EXISTS modules_educacenso_cod_turma_cod_turma_cod_turma_inep_unique');
        DB::statement('DROP INDEX IF EXISTS modules.modules_educacenso_cod_turma_cod_turma_cod_turma_inep_unique;');
        Schema::table('modules.educacenso_cod_turma', function (Blueprint $table) {
            $table->integer('turno_id')->nullable();
            $table->foreign('turno_id')
                ->references('id')
                ->on('pmieducar.turma_turno')
                ->onDelete('cascade');

            $table->unique([
                'cod_turma',
                'cod_turma_inep',
                'turno_id'
            ]);
        });
    }

    public function down(): void
    {
        DB::unprepared('ALTER TABLE IF EXISTS modules.educacenso_cod_turma DROP CONSTRAINT IF EXISTS modules_educacenso_cod_turma_cod_turma_cod_turma_inep_turno_id_');
        Schema::table('modules.educacenso_cod_turma', function (Blueprint $table) {
            $table->dropColumn('turno_id');

            $table->unique([
                'cod_turma',
                'cod_turma_inep'
            ]);
        });
    }
};
