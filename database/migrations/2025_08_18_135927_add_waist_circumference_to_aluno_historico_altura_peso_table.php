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
        Schema::table('pmieducar.aluno_historico_altura_peso', function (Blueprint $table) {
            $table->decimal('circunferencia_cintura', 5, 2)->nullable()->after('peso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pmieducar.aluno_historico_altura_peso', function (Blueprint $table) {
            $table->dropColumn('circunferencia_cintura');
        });
    }
};
