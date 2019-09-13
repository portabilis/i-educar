<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogUnificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_unifications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->integer('main_id');
            $table->json('duplicates_id');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('log_unifications');
    }
}
