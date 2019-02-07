<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InserirTiposLogradouros extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('urbano.tipo_logradouro')->insert([
            'idtlog' => 'POVO',
            'descricao' => 'Povoado']);

        DB::table('urbano.tipo_logradouro')->insert([
            'idtlog' => 'LOCAL',
            'descricao' => 'Localidade']);
    }
}
