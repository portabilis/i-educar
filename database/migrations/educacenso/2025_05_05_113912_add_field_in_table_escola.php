<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pmieducar.escola', function (Blueprint $table) {
            $table->smallInteger('numero_salas_cantinho_leitura')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('pmieducar.escola', function (Blueprint $table) {
            $table->dropColumn('numero_salas_cantinho_leitura');
        });
    }
};
