<?php

use iEducar\Modules\Educacenso\Migrations\MigraDadosCartorio as MigraDadosCartorioClass;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigraDadosCartorio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cadastro.documento', function (Blueprint $table) {
            $table->string('cartorio_cert_civil', 255)->change();
        });

        MigraDadosCartorioClass::execute();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cadastro.documento', function (Blueprint $table) {
            $table->string('cartorio_cert_civil', 200)->change();
        });
    }
}
