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
        Schema::table('modules.rota_transporte_escolar', function (Blueprint $table) {
            $table->unsignedBigInteger('ref_idpes')->nullable();
            $table->foreign('ref_idpes')->references('idpes')->on('cadastro.fisica');
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
};
