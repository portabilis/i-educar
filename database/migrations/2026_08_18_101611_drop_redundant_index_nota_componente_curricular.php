<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP INDEX IF EXISTS modules.idx_nota_componente_curricular_etapa');
    }

    public function down(): void
    {
        Schema::table('modules.nota_componente_curricular', function (Blueprint $table) {
            $table->index(['nota_aluno_id', 'componente_curricular_id', 'etapa'], 'idx_nota_componente_curricular_etapa');
        });
    }
};
