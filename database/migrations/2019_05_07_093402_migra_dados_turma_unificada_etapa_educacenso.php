<?php

use iEducar\Modules\Educacenso\Migrations\MigraDadosTurmaUnificadaEtapaEducacenso as MigraDadosTurmaUnificadaEtapaEducacensoClass;
use Illuminate\Database\Migrations\Migration;

class MigraDadosTurmaUnificadaEtapaEducacenso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        MigraDadosTurmaUnificadaEtapaEducacensoClass::execute();
    }
}
