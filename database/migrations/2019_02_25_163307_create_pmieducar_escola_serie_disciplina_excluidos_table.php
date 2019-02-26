<?php

use App\Support\Database\WhenDeleted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarEscolaSerieDisciplinaExcluidosTable extends Migration
{
    use WhenDeleted;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pmieducar.escola_serie_disciplina_excluidos', function (Blueprint $table) {
            $table->integer('ref_ref_cod_serie');
            $table->integer('ref_ref_cod_escola');
            $table->integer('ref_cod_disciplina');
            $table->integer('ativo');
            $table->integer('carga_horaria')->nullable();
            $table->integer('etapas_especificas')->nullable();
            $table->string('etapas_utilizadas')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::unprepared(
            '
                ALTER TABLE pmieducar.escola_serie_disciplina_excluidos 
                ADD COLUMN anos_letivos int2[] NOT NULL DEFAULT \'{}\'::smallint[]
            '
        );

        $this->whenDeletedMoveTo('pmieducar.escola_serie_disciplina', 'pmieducar.escola_serie_disciplina_excluidos', [
            'ref_ref_cod_serie',
            'ref_ref_cod_escola',
            'ref_cod_disciplina',
            'ativo',
            'carga_horaria',
            'etapas_especificas',
            'etapas_utilizadas',
            'anos_letivos',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropTriggerWhenDeleted('pmieducar.escola_serie_disciplina');

        Schema::dropIfExists('pmieducar.escola_serie_disciplina_excluidos');
    }
}
