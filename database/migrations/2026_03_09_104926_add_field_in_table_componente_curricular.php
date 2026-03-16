<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules.componente_curricular', function (Blueprint $table) {
            $table->string('color', 10)->default('#FFFFFF');
        });
    }

    public function down(): void
    {
        Schema::table('modules.componente_curricular', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
