<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pmieducar.turma', function (Blueprint $table) {
            $table->time('hora_inicial_matutino')->nullable();
            $table->time('hora_inicio_intervalo_matutino')->nullable();
            $table->time('hora_fim_intervalo_matutino')->nullable();
            $table->time('hora_final_matutino')->nullable();
            $table->time('hora_inicial_vespertino')->nullable();
            $table->time('hora_inicio_intervalo_vespertino')->nullable();
            $table->time('hora_fim_intervalo_vespertino')->nullable();
            $table->time('hora_final_vespertino')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pmieducar.turma', function (Blueprint $table) {
            $table->dropColumn('hora_inicial_matutino');
            $table->dropColumn('hora_inicio_intervalo_matutino');
            $table->dropColumn('hora_fim_intervalo_matutino');
            $table->dropColumn('hora_final_matutino');
            $table->dropColumn('hora_inicial_vespertino');
            $table->dropColumn('hora_inicio_intervalo_vespertino');
            $table->dropColumn('hora_fim_intervalo_vespertino');
            $table->dropColumn('hora_final_vespertino');
        });
    }
};
