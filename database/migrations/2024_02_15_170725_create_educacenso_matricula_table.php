<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('modules.educacenso_matricula', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('matricula_turma_id')->references('id')->on('pmieducar.matricula_turma');
            $table->bigInteger('matricula_inep')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules.educacenso_matricula');
    }
};
