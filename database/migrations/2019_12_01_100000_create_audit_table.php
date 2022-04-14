<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ieducar_audit', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('context')->nullable();
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->string('schema');
            $table->string('table');
            $table->timestamp('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ieducar_audit');
    }
}
