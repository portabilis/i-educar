<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateExporterStagesView extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->dropView('public.exporter_stages');
        $this->dropView('public.exporter_school_stages');
        $this->dropView('public.exporter_school_class_stages');

        $this->createView('public.exporter_school_stages');
        $this->createView('public.exporter_school_class_stages');
        $this->createView('public.exporter_stages');
    }

    public function down(): void
    {
        $this->dropView('public.exporter_stages');
        $this->dropView('public.exporter_school_stages');
        $this->dropView('public.exporter_school_class_stages');
    }
}
