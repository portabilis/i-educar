<?php

use App\Support\Database\DropForeignKey;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropForeignKeysInMunicipioTable extends Migration
{
    use DropForeignKey;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropForeignKeysFromTable('municipio');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('public.bairro', function (Blueprint $table) {
            $table->foreign('idmun')
                ->references('idmun')
                ->on('municipio');
        });

        Schema::table('public.distrito', function (Blueprint $table) {
            $table->foreign('idmun')
                ->references('idmun')
                ->on('municipio');
        });

        Schema::table('cadastro.fisica', function (Blueprint $table) {
            $table->foreign('idmun_nascimento')
                ->references('idmun')
                ->on('municipio');
        });

        Schema::table('public.municipio', function (Blueprint $table) {
            $table->foreign('idmun_pai')
                ->references('idmun')
                ->on('municipio');

            $table->foreign('idmun')
                ->references('idmun')
                ->on('municipio');
        });
    }
}
