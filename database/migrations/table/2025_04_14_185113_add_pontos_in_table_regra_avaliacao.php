<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules.regra_avaliacao', function (Blueprint $table) {
            $table->string('pontos')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('modules.regra_avaliacao', function (Blueprint $table) {
            $table->dropColumn('pontos');
        });
    }
};
