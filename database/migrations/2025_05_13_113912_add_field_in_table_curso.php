<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pmieducar.curso', function (Blueprint $table) {
            $table->boolean('bloquear_novas_matriculas')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('pmieducar.curso', function (Blueprint $table) {
            $table->dropColumn('bloquear_novas_matriculas');
        });
    }
};
