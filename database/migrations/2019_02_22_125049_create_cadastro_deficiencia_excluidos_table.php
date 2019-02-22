<?php

use App\Support\Database\WhenDeleted;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCadastroDeficienciaExcluidosTable extends Migration
{
    use WhenDeleted;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cadastro.deficiencia_excluidos', function (Blueprint $table) {
            $table->integer('cod_deficiencia')->primary();
            $table->string('nm_deficiencia');
            $table->integer('deficiencia_educacenso')->nullable();
            $table->boolean('desconsidera_regra_diferenciada')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $this->whenDeletedMoveTo('cadastro.deficiencia', 'cadastro.deficiencia_excluidos', [
            'cod_deficiencia',
            'nm_deficiencia',
            'deficiencia_educacenso',
            'desconsidera_regra_diferenciada',
            'updated_at',
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

        Schema::dropIfExists('cadastro.deficiencia_excluidos');
    }
}
