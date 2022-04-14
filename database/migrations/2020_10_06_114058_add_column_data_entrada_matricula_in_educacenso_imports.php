<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDataEntradaMatriculaInEducacensoImports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('public.educacenso_imports', function (Blueprint $table) {
            $table->date('registration_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('public.educacenso_imports', function (Blueprint $table) {
            $table->dropColumn('registration_date');
        });
    }
}
