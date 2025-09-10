<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pmieducar.turma', 'etapa_agregada')) {
            Schema::table('pmieducar.turma', function (Blueprint $table) {
                $table->smallInteger('etapa_agregada')->nullable();
            });
        }

        if (!Schema::hasColumn('pmieducar.turma', 'classe_especial')) {
            Schema::table('pmieducar.turma', function (Blueprint $table) {
                $table->smallInteger('classe_especial')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::table('pmieducar.turma', function (Blueprint $table) {
            $table->dropColumn('etapa_agregada');
            $table->dropColumn('classe_especial');
        });
    }
};
