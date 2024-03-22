<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pmieducar.aluno', function (Blueprint $table) {
            $table->text('rota_transporte')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pmieducar.aluno', function (Blueprint $table) {
            $table->dropColumn('rota_transporte');
        });
    }
};
