<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration
{
    use AsView;

    public function up()
    {
        $this->dropView('public.exporter_social_assistance');
        $this->dropView('public.exporter_student');
        $this->dropView('public.exporter_student_grouped_registration');
        $this->dropView('public.exporter_teacher');
        $this->dropView('public.exporter_person');

        $this->createView('public.exporter_person', '2023-10-05');
        $this->createView('public.exporter_teacher', '2023-06-19');
        $this->createView('public.exporter_student_grouped_registration', '2023-04-20');
        $this->createView('public.exporter_student', '2022-06-15');
        $this->createView('public.exporter_social_assistance', '2020-05-07');
    }

    public function down()
    {
        $this->dropView('public.exporter_social_assistance');
        $this->dropView('public.exporter_student');
        $this->dropView('public.exporter_student_grouped_registration');
        $this->dropView('public.exporter_teacher');
        $this->dropView('public.exporter_person');

        $this->createView('public.exporter_person', '2023-10-04');
        $this->createView('public.exporter_teacher', '2023-06-19');
        $this->createView('public.exporter_student_grouped_registration', '2023-04-20');
        $this->createView('public.exporter_student', '2022-06-15');
        $this->createView('public.exporter_social_assistance', '2020-05-07');
    }
};
