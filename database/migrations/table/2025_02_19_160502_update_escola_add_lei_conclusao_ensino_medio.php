<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pmieducar.escola', function (Blueprint $table) {
            $table->string('lei_conclusao_ensino_medio')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pmieducar.escola', function (Blueprint $table) {
            $table->dropColumn('lei_conclusao_ensino_medio');
        });
    }
};
