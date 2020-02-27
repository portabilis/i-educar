<?php

use App\Support\Database\DropForeignKey;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropForeignKeysInCepLogradouroTable extends Migration
{
    use DropForeignKey;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropForeignKeysFromTable('cep_logradouro');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('urbano.cep_logradouro_bairro', function (Blueprint $table) {
            $table->foreign(['cep', 'idlog'])
                ->references(['cep', 'idlog'])
                ->on('urbano.cep_logradouro')
                ->onDelete('cascade');
        });
    }
}
