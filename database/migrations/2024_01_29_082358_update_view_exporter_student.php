<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->dropView('public.exporter_social_assistance');
        $this->dropView('public.exporter_student');
        $this->createView('public.exporter_student', '2024-01-29');
        $this->createView('public.exporter_social_assistance', '2020-05-07');
    }

    public function down(): void
    {
        $this->dropView('public.exporter_social_assistance');
        $this->dropView('public.exporter_student');
        $this->createView('public.exporter_student', '2024-01-03');
        $this->createView('public.exporter_social_assistance', '2020-05-07');
    }
};
