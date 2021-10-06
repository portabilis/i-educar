<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateCadastroEnderecoExternoView extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createView('cadastro.endereco_externo');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('cadastro.endereco_externo');
    }
}
