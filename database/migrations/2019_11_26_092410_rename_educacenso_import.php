<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameEducacensoImport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('educacenso_import');
        Schema::dropIfExists('educacenso_imports');

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
        //
    }
}
