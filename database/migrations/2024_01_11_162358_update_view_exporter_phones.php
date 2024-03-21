<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->createView('public.exporter_phones', '2024-01-11');
    }

    public function down(): void
    {
        $this->dropView('public.exporter_phones');
        $this->createView('public.exporter_phones', '2020-04-01');
    }
};
