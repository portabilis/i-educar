<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateCadastroVPessoafjCountView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            'DROP VIEW IF EXISTS cadastro.v_pessoafj_count;'
        );

        DB::unprepared(
            file_get_contents(__DIR__ . '/../../sqls/views/cadastro.v_pessoafj_count.sql')
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
            'DROP VIEW IF EXISTS cadastro.v_pessoafj_count;'
        );
    }
}
