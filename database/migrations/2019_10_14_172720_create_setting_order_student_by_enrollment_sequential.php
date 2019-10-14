<?php

use App\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingOrderStudentByEnrollmentSequential extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Setting::create([
            'key' => 'legacy.report.order_student_by_enrollment_sequential',
            'value' => '0',
            'type' => 'string',
            'description' => 'Indica se os alunos serão ordenados pelo campo sequencial_fechamento de matricula_turma'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Setting::where('key', 'legacy.report.order_student_by_enrollment_sequential')->delete();
    }
}
