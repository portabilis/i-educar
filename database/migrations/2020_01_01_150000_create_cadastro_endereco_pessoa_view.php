<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateCadastroEnderecoPessoaView extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createView('cadastro.endereco_pessoa');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('cadastro.endereco_pessoa');
    }
}
