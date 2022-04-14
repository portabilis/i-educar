<?php

use App\Support\Database\WhenDeleted;
use Illuminate\Database\Migrations\Migration;

class AddTriggerDeletedAtInPmieducarAlunoExcluidosTable extends Migration
{
    use WhenDeleted;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->whenDeletedMoveTo('pmieducar.aluno', 'pmieducar.aluno_excluidos', [
            'cod_aluno',
            'ref_idpes',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropTriggerWhenDeleted('pmieducar.aluno');
    }
}
