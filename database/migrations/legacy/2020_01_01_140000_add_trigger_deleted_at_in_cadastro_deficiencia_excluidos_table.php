<?php

use App\Support\Database\WhenDeleted;
use Illuminate\Database\Migrations\Migration;

class AddTriggerDeletedAtInCadastroDeficienciaExcluidosTable extends Migration
{
    use WhenDeleted;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->whenDeletedMoveTo('cadastro.deficiencia', 'cadastro.deficiencia_excluidos', [
            'cod_deficiencia',
            'nm_deficiencia',
            'deficiencia_educacenso',
            'desconsidera_regra_diferenciada',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropTriggerWhenDeleted('cadastro.deficiencia');
    }
}
