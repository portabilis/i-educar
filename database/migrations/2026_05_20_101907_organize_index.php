<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP INDEX IF EXISTS modules.idx_nota_componente_curricular_etp');
        DB::unprepared('DROP INDEX IF EXISTS modules.modules_nota_componente_curricular_id_index;');
        DB::unprepared('DROP INDEX IF EXISTS modules.modules_nota_componente_curricular_media_etapa_index;');
        DB::unprepared('DROP INDEX IF EXISTS cadastro.cadastro_pessoa_slug_index;');
    }
};
