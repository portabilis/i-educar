<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FrequenciaInformacoes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.frequencia_informacoes', function (Blueprint $table) {
            $table->id();
            $table->integer('ref_frequencia');
            $table->integer('dias_letivos')->nullable();
            $table->integer('dias_realizados')->nullable();
            $table->integer('dias_realizar')->nullable();
            $table->integer('ch')->nullable();
            $table->integer('aulas_realizadas')->nullable();
            $table->integer('aulas_realizar')->nullable();
            $table->integer('tipo_turma')->nullable();

       

           
        });   
    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
