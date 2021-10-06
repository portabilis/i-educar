<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateUrbanoCepLogradouroBairroView extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createView('urbano.cep_logradouro_bairro');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('urbano.cep_logradouro_bairro');
    }
}
