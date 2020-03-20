<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class DropCadastroEnderecoExternoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            '
                drop table if exists cadastro.endereco_externo cascade;
            '
        );
    }
}
