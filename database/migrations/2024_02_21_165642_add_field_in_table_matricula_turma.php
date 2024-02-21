<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pmieducar.matricula_turma', function (Blueprint $table) {
            $table->boolean('desconsiderar_educacenso')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('pmieducar.matricula_turma', function (Blueprint $table) {
            $table->dropColumn('desconsiderar_educacenso');
        });
    }
};
