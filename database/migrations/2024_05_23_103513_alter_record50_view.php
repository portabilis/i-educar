<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->dropView('public.educacenso_record50');
        $this->createView('public.educacenso_record50', '2024-05-23');
    }

    public function down(): void
    {
        $this->dropView('public.educacenso_record50');
        $this->createView('public.educacenso_record50', '2023-05-17');
    }
};
