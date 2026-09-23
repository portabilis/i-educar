<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cadastro.fisica', function (Blueprint $table) {
            $table->unsignedSmallInteger('document_absence_reason_id')->nullable();
            $table->text('document_absence_observation')->nullable();

            $table->foreign('document_absence_reason_id')
                ->references('id')
                ->on('document_absence_reasons');
        });
    }

    public function down(): void
    {
        Schema::table('cadastro.fisica', function (Blueprint $table) {
            $table->dropForeign(['document_absence_reason_id']);
            $table->dropColumn('document_absence_reason_id');
            $table->dropColumn('document_absence_observation');
        });
    }
};
