<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_exports', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('hash');
            $table->string('filename');
            $table->integer('size')->nullable();
            $table->string('url')->nullable();
            $table->smallInteger('status_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_exports');
    }
};
