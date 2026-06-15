<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pmieducar.matricula', function (Blueprint $table) {
            $table->boolean('deixou_de_frequentar_idade_obrigatoria')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('pmieducar.matricula', function (Blueprint $table) {
            $table->dropColumn('deixou_de_frequentar_idade_obrigatoria');
        });
    }
};
