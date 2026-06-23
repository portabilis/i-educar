<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pmieducar.instituicao', function (Blueprint $table) {
            $table->boolean('proibir_reclassificacao_educacao_infantil_primeiro_ano')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('pmieducar.instituicao', function (Blueprint $table) {
            $table->dropColumn('proibir_reclassificacao_educacao_infantil_primeiro_ano');
        });
    }
};
