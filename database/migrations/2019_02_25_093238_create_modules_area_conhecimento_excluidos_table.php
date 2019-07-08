<?php

use App\Support\Database\WhenDeleted;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulesAreaConhecimentoExcluidosTable extends Migration
{
    use WhenDeleted;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.area_conhecimento_excluidos', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('instituicao_id');
            $table->string('nome');
            $table->string('secao')->nullable();
            $table->integer('ordenamento_ac')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $this->whenDeletedMoveTo('modules.area_conhecimento', 'modules.area_conhecimento_excluidos', [
            'id',
            'instituicao_id',
            'nome',
            'secao',
            'ordenamento_ac',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropTriggerWhenDeleted('modules.area_conhecimento');

        Schema::dropIfExists('modules.area_conhecimento_excluidos');
    }
}
