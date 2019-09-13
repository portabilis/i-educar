<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateCadastroVPessoaFisicaView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            'DROP VIEW IF EXISTS cadastro.v_pessoa_fisica;'
        );
        
        DB::unprepared(
            file_get_contents(__DIR__ . '/../../sqls/views/cadastro.v_pessoa_fisica.sql')
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared(
            'DROP VIEW IF EXISTS cadastro.v_pessoa_fisica;'
        );
    }
}
