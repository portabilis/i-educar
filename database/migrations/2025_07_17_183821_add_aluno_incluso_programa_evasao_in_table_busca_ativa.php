<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pmieducar.busca_ativa', function (Blueprint $table) {
            $table->boolean('aluno_incluso_programa_evasao')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pmieducar.busca_ativa', function (Blueprint $table) {
            $table->dropColumn('aluno_incluso_programa_evasao');
        });
    }
};
