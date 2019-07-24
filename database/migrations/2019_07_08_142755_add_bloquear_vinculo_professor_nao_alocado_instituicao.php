<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBloquearVinculoProfessorNaoAlocadoInstituicao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.instituicao', function(Blueprint $table){
            $table->boolean('bloquear_vinculo_professor_sem_alocacao_escola')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.instituicao', function(Blueprint $table){
            $table->dropColumn('bloquear_vinculo_professor_sem_alocacao_escola');
        });
    }
}
