<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEducacensoImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('public.educacenso_imports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('year');
            $table->string('school');
            $table->integer('user_id');
            $table->boolean('finished');
            $table->boolean('error')->default(false);
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
        Schema::dropIfExists('public.educacenso_imports');
    }
}
