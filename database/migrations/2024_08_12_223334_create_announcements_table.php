<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('repeat_on_login')->default(false);
            $table->boolean('show_confirmation')->default(false);
            $table->boolean('show_vacancy')->default(false);
            $table->unsignedInteger('created_by_user_id')->nullable();
            $table->foreign('created_by_user_id')->references('cod_usuario')->on('pmieducar.usuario')->onDelete('cascade')->onUpdate('cascade');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('announcement_user_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('announcement_id');
            $table->foreign('announcement_id')->references('id')->on('announcements')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('user_type_id');
            $table->foreign('user_type_id')->references('cod_tipo_usuario')->on('pmieducar.tipo_usuario')->onDelete('cascade')->onUpdate('cascade');
            $table->primary([
                'user_type_id',
                'announcement_id',
            ]);
        });

        Schema::create('announcement_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('announcement_id');
            $table->foreign('announcement_id')->references('id')->on('announcements')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('cod_usuario')->on('pmieducar.usuario')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->primary([
                'user_id',
                'announcement_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_users');
        Schema::dropIfExists('announcement_user_types');
        Schema::dropIfExists('announcements');
    }
};
