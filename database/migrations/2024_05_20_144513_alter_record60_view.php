<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->dropView('public.educacenso_record60');
        $this->createView('public.educacenso_record60', '2024-05-20');
    }

    public function down(): void
    {
        $this->dropView('public.educacenso_record60');
        $this->createView('public.educacenso_record60', '2023-05-17');
    }
};
