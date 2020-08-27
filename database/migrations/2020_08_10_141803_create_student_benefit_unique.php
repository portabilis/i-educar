<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentBenefitUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aluno_aluno_beneficio', function (Blueprint $table) {
            $table->unique([
                'aluno_id',
                'aluno_beneficio_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aluno_aluno_beneficio', function (Blueprint $table) {
            $table->dropUnique([
                'aluno_id',
                'aluno_beneficio_id',
            ]);
        });
    }
}
