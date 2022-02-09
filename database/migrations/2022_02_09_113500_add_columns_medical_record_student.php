<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsMedicalRecordStudent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.ficha_medica_aluno', function (Blueprint $table) {
            $table->char('vacina_covid', 1)->nullable();
            $table->integer('desc_vacina_covid')->nullable();
            
            
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
            $table->dropColumn(['vacina_covid', 'desc_vacina_covid']);
        });
    }
}
