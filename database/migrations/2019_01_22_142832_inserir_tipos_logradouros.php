<?php

use Illuminate\Support\Facades\DB;
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
        DB::table('urbano.tipo_logradouro')->updateOrInsert([
            'idtlog' => 'POVO',
        ],
            [
            'descricao' => 'Povoado'
        ]);

        DB::table('urbano.tipo_logradouro')->updateOrInsert([
            'idtlog' => 'LOCAL',
        ], [
            'descricao' => 'Localidade'
        ]);
    }
}
