<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateExporterTeacherDisciplineView extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->dropView('public.exporter_teacher_disciplines');
        $this->dropView('public.exporter_teacher_disciplines');
    }

    public function down(): void
    {
        $this->dropView('public.exporter_teacher_disciplines');
    }
}
