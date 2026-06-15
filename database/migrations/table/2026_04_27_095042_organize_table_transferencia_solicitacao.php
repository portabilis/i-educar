<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pmieducar.transferencia_solicitacao', function (Blueprint $table) {
            $table->index(['ativo', 'ref_cod_matricula_saida']);
        });
    }

    public function down(): void
    {
        Schema::table('pmieducar.transferencia_solicitacao', function (Blueprint $table) {
            $table->dropIndex(['ativo', 'ref_cod_matricula_saida']);
        });
    }
};
