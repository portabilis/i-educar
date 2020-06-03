<?php

use App\Support\Database\DropForeignKey;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropForeignKeysInPaisTable extends Migration
{
    use DropForeignKey;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropForeignKeysFromTable('pais');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('public.uf', function (Blueprint $table) {
            $table->foreign('idpais')
                ->references('idpais')
                ->on('pais');
        });

        Schema::table('cadastro.fisica', function (Blueprint $table) {
            $table->foreign('idpais_estrangeiro')
                ->references('idpais')
                ->on('pais');
        });
    }
}
