<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules.nota_componente_curricular', function (Blueprint $table) {
            $table->index(['id']);
        });
    }

    public function down(): void
    {
        Schema::table('modules.nota_componente_curricular', function (Blueprint $table) {
            $table->dropIndex(['id']);
        });
    }
};
