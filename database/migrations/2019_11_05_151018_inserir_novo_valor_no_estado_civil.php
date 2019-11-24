<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InserirNovoValorNoEstadoCivil extends Migration
{
    /**
     * Retorna o maior ideciv
     *
     * @return mixed
     */
    private function getEstadoCivil()
    {
        $estado_civil = new clsEstadoCivil();
        $estado_civil_lista = $estado_civil->lista();

        $max = $estado_civil_lista[0]['ideciv'];
        foreach ($estado_civil_lista as $key => $value) {
            if ($value['ideciv'] > $max) {
                $max = $value['ideciv'];
            }
        }
        return $max;
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $ideciv = $this->getEstadoCivil() + 1;

        DB::table('cadastro.estado_civil')->insert(['ideciv' => $ideciv, 'descricao' => 'Não informado']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('cadastro.estado_civil')
            ->where('descricao','Não informado')
            ->delete();
    }
}
