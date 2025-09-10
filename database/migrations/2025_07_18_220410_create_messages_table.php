<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->morphs('messageable');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('description');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('cod_usuario')
                ->on('pmieducar.usuario')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
