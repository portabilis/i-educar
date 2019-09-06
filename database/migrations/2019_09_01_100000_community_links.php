<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CommunityLinks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('pmieducar.configuracoes_gerais')->where('ref_cod_instituicao', 1)->update([
            'ieducar_login_footer' => '<p>Comunidade i-Educar - <a class="light" href="https://forum.ieducar.org/" target="_blank"> Obter Suporte </a></p>',
            'ieducar_external_footer' => '<p>Conheça mais sobre o i-Educar, acesse nosso <a href="https://ieducar.org/blog/">blog</a>.</p>',
            'ieducar_internal_footer' => '<p>Conheça mais sobre o i-Educar, acesse nosso <a href="https://ieducar.org/blog/">blog</a>.</p>',
        ]);
    }
}
