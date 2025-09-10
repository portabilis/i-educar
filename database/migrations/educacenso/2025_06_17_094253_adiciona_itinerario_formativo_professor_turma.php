<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    use AsView;

    public function up(): void
    {
        DB::statement('ALTER TABLE IF EXISTS modules.professor_turma ADD COLUMN area_itinerario smallint[];');
        DB::statement('ALTER TABLE IF EXISTS modules.professor_turma ADD COLUMN leciona_itinerario_tecnico_profissional smallint');

        $this->dropView('public.educacenso_record50');
        $this->createView('public.educacenso_record50', '2025-06-17');
    }

    public function down(): void
    {
        $this->dropView('public.educacenso_record50');
        $this->createView('public.educacenso_record50', '2025-06-16');

        DB::statement('ALTER TABLE IF EXISTS modules.professor_turma DROP COLUMN leciona_itinerario_tecnico_profissional;');
        DB::statement('ALTER TABLE IF EXISTS modules.professor_turma DROP COLUMN area_itinerario;');
    }
};
