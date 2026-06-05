<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cadastro.fisica', function (Blueprint $table) {
            $table->boolean('pai_restricao_judicial')->default(false);
            $table->boolean('mae_restricao_judicial')->default(false);
            $table->boolean('responsavel_restricao_judicial')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('cadastro.fisica', function (Blueprint $table) {
            $table->dropColumn('pai_restricao_judicial');
            $table->dropColumn('mae_restricao_judicial');
            $table->dropColumn('responsavel_restricao_judicial');
        });
    }
};
