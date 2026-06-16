<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateExporterDatabaseView extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->createView('public.exporter_person');
        $this->createView('public.exporter_student');
        $this->createView('public.exporter_student_grouped_registration');
        $this->createView('public.exporter_teacher');
        $this->createView('public.exporter_teacher_disciplines');
        $this->createView('public.exporter_employee');
        $this->createView('public.exporter_social_assistance');
        $this->createView('public.exporter_benefits');
        $this->createView('public.exporter_disabilities');
        $this->createView('public.exporter_projects');
    }

    public function down(): void
    {
        $this->dropView('public.exporter_projects');
        $this->dropView('public.exporter_disabilities');
        $this->dropView('public.exporter_benefits');
        $this->dropView('public.exporter_social_assistance');
        $this->dropView('public.exporter_employee');
        $this->dropView('public.exporter_teacher_disciplines');
        $this->dropView('public.exporter_teacher');
        $this->dropView('public.exporter_student_grouped_registration');
        $this->dropView('public.exporter_student');
        $this->dropView('public.exporter_person');
    }
}
