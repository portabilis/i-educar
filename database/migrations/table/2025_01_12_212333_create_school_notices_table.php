<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_notices', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('institution_id');
            $table->foreign('institution_id')->references('cod_instituicao')->on('pmieducar.instituicao');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('cod_usuario')->on('pmieducar.usuario');
            $table->unsignedSmallInteger('school_id');
            $table->foreign('school_id')->references('cod_escola')->on('pmieducar.escola');
            $table->string('title');
            $table->text('description');
            $table->date('date');
            $table->time('hour');
            $table->string('local');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_notices');
    }
};
