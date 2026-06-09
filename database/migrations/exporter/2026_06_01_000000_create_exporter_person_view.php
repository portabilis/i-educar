<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateExporterPersonView extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->dropView('public.exporter_social_assistance');
        $this->dropView('public.exporter_student_grouped_registration');
        $this->dropView('public.exporter_employee');
        $this->dropView('public.exporter_teacher');
        $this->dropView('public.exporter_student');
        $this->dropView('public.exporter_person');

        $this->createView('public.exporter_person');
        $this->createView('public.exporter_student');
        $this->createView('public.exporter_student_grouped_registration');
        $this->createView('public.exporter_teacher');
        $this->createView('public.exporter_employee');
        $this->createView('public.exporter_social_assistance');
    }

    public function down(): void
    {
        $this->dropView('public.exporter_social_assistance');
        $this->dropView('public.exporter_student_grouped_registration');
        $this->dropView('public.exporter_employee');
        $this->dropView('public.exporter_teacher');
        $this->dropView('public.exporter_student');
        $this->dropView('public.exporter_person');
    }
}
