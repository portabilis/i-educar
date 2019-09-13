<?php

use App\Support\Database\WhenDeleted;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarAlunoExcluidosTable extends Migration
{
    use WhenDeleted;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pmieducar.aluno_excluidos', function (Blueprint $table) {
            $table->integer('cod_aluno')->primary();
            $table->integer('ref_idpes')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });

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

        Schema::dropIfExists('pmieducar.aluno_excluidos');
    }
}
