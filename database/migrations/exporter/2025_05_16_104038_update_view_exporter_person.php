<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use AsView;

    public function up()
    {
        $this->dropView('public.exporter_student_grouped_registration');
        $this->dropView('public.exporter_employee');
        $this->dropView('public.exporter_social_assistance');
        $this->dropView('public.exporter_student');
        $this->dropView('public.exporter_teacher');
        $this->dropView('public.exporter_person');

        $this->createView('public.exporter_person', '2025-05-16'); //
        $this->createView('public.exporter_student_grouped_registration', '2025-05-16'); //
        $this->createView('public.exporter_employee', '2023-11-16');
        $this->createView('public.exporter_student', '2025-05-16'); //
        $this->createView('public.exporter_social_assistance', '2020-05-07');
        $this->createView('public.exporter_teacher', '2024-10-23');
    }

    public function down()
    {
        $this->dropView('public.exporter_student_grouped_registration');
        $this->dropView('public.exporter_employee');
        $this->dropView('public.exporter_social_assistance');
        $this->dropView('public.exporter_student');
        $this->dropView('public.exporter_teacher');
        $this->dropView('public.exporter_person');

        $this->createView('public.exporter_person', '2025-03-11');
        $this->createView('public.exporter_student_grouped_registration', '2025-03-11');
        $this->createView('public.exporter_employee', '2023-11-16');
        $this->createView('public.exporter_student', '2024-05-06');
        $this->createView('public.exporter_social_assistance', '2020-05-07');
        $this->createView('public.exporter_teacher', '2024-10-23');
    }
};
