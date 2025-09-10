<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cadastro.fisica', function (Blueprint $table): void {
            $table->foreignId('povo_indigena_educacenso_id')->nullable()->constrained()
                ->references('id')
                ->on('modules.povo_indigena_educacenso');
        });

    }

    public function down(): void
    {
        Schema::table('cadastro.fisica', function (Blueprint $table): void {
            $table->dropForeign(['povo_indigena_educacenso_id']);
            $table->dropColumn('povo_indigena_educacenso_id');
        });
    }
};
