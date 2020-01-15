<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class DropCadastroVEnderecoView extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('cadastro.v_endereco');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('cadastro.v_endereco');
        $this->createView('cadastro.v_endereco', '2019-09-06');
    }
}
