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
            $table->char('aceita_hospital_proximo', 1)->nullable();
            $table->string('desc_aceita_hospital_proximo')->nullable();
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
            $table->dropColumn(['aceita_hospital_proximo', 'desc_aceita_hospital_proximo']);
        });
    }
}
