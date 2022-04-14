<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBNCCSpecificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.bncc_especificacao', function (Blueprint $table) {
            $table->integer('bncc_id');
            $table->text('especificacao');
            $table->id();
            
            $table->foreign('bncc_id')
                ->references('id')
                ->on('modules.bncc')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modules.bncc_especificacao');
    }
}
