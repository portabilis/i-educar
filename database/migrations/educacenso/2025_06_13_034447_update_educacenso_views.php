<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->dropView('public.educacenso_record20');
        $this->dropView('public.educacenso_record50');
        $this->dropView('public.educacenso_record60');

        $this->createView('public.educacenso_record20', '2025-06-13');
        $this->createView('public.educacenso_record50', '2025-06-13');
        $this->createView('public.educacenso_record60', '2025-06-13');
    }

    public function down(): void
    {
        $this->dropView('public.educacenso_record20');
        $this->dropView('public.educacenso_record50');
        $this->dropView('public.educacenso_record60');

        $this->createView('public.educacenso_record20', '2025-06-11');
        $this->createView('public.educacenso_record50', '2025-06-11');
        $this->createView('public.educacenso_record60', '2025-06-11');
    }
};
