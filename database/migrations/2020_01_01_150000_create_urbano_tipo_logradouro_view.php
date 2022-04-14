<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateUrbanoTipoLogradouroView extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createView('urbano.tipo_logradouro');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('urbano.tipo_logradouro');
    }
}
