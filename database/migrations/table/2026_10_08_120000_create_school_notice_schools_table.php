<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_notice_schools', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('school_notice_id');
            $table->foreign('school_notice_id')
                ->references('id')
                ->on('school_notices')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unsignedInteger('school_id');
            $table->foreign('school_id')
                ->references('cod_escola')
                ->on('pmieducar.escola')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unique(['school_notice_id', 'school_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_notice_schools');
    }
};
