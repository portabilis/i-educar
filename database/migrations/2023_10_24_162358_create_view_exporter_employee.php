<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->createView('public.exporter_employee', '2023-11-16');
    }

    public function down(): void
    {
        $this->dropView('public.exporter_employee');
    }
};
