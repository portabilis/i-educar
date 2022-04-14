<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnsMedicalRecordStudent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.ficha_medica_aluno', function (Blueprint $table) {
            $table->dropColumn('hospital_clinica');
            $table->dropColumn('hospital_clinica_endereco');
            $table->dropColumn('hospital_clinica_telefone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.ficha_medica_aluno', function (Blueprint $table) {
            $table->string('hospital_clinica')->nullable();
            $table->string('hospital_clinica_endereco')->nullable();
            $table->string('hospital_clinica_telefone')->nullable();
        });
    }
}
