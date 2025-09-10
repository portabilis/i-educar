<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->dropView('public.educacenso_record20');
        $this->createView('public.educacenso_record20', '2025-06-02');
    }

    public function down(): void
    {
        $this->dropView('public.educacenso_record20');
        $this->createView('public.educacenso_record20', '2024-05-20');
    }
};
