<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AdicionaEstadoCivilNaoInformado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('cadastro.estado_civil')->updateOrInsert([
            'ideciv' => 7,
        ], [
            'descricao' => 'Não informado',
        ]);
    }
}
