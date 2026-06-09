<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreatePublicExporterPhonesView extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->dropView('public.exporter_phones');
        $this->createView('public.exporter_phones');
    }

    public function down(): void
    {
        $this->dropView('public.exporter_phones');
    }
}
