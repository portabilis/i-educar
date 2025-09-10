<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->dropView('public.exporter_social_assistance');
        $this->dropView('public.exporter_student_grouped_registration');
        $this->dropView('public.exporter_student');
        $this->dropView('public.educacenso_record50');
        $this->dropView('public.educacenso_record20');
        $this->dropView('public.educacenso_record60');

        DB::statement('ALTER TABLE pmieducar.turma ALTER COLUMN tipo_atendimento TYPE integer[] USING CASE WHEN tipo_atendimento IS NOT NULL THEN ARRAY[tipo_atendimento]::integer[] ELSE ARRAY[]::integer[] END;');

        $this->createView('public.educacenso_record50', '2024-05-23');
        $this->createView('public.educacenso_record20', '2025-06-02');
        $this->createView('public.educacenso_record60', '2025-05-07');
        $this->createView('public.exporter_student', '2025-05-16');
        $this->createView('public.exporter_student_grouped_registration', '2025-05-16');
        $this->createView('public.exporter_social_assistance', '2020-05-07');
    }

    public function down(): void
    {
        $this->dropView('public.exporter_social_assistance');
        $this->dropView('public.exporter_student_grouped_registration');
        $this->dropView('public.exporter_student');
        $this->dropView('public.educacenso_record50');
        $this->dropView('public.educacenso_record20');
        $this->dropView('public.educacenso_record60');

        DB::statement('ALTER TABLE pmieducar.turma ALTER COLUMN tipo_atendimento TYPE smallint USING CASE WHEN tipo_atendimento IS NOT NULL AND array_length(tipo_atendimento, 1) > 0 THEN (tipo_atendimento[1])::smallint ELSE NULL END;');

        $this->createView('public.educacenso_record50', '2024-05-23');
        $this->createView('public.educacenso_record20', '2025-06-02');
        $this->createView('public.educacenso_record60', '2025-05-07');
        $this->createView('public.exporter_student', '2025-05-16');
        $this->createView('public.exporter_student_grouped_registration', '2025-05-16');
        $this->createView('public.exporter_social_assistance', '2020-05-07');
    }
};
