<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('pmieducar.bloqueio_lancamento_faltas_notas');

        DB::unprepared('DROP SEQUENCE IF EXISTS public.bloqueio_lancamento_faltas_notas_seq;');
    }
};
