<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateEducacensoRecordsView extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->down();

        $this->createView('public.educacenso_record20');
        $this->createView('public.educacenso_record40');
        $this->createView('public.educacenso_record50');
        $this->createView('public.educacenso_record60');
    }

    public function down(): void
    {
        $this->dropView('public.educacenso_record20');
        $this->dropView('public.educacenso_record40');
        $this->dropView('public.educacenso_record50');
        $this->dropView('public.educacenso_record60');
    }
}
