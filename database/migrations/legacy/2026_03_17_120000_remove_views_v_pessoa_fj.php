<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP VIEW IF EXISTS cadastro.v_pessoa_fj;');
        DB::unprepared('DROP VIEW IF EXISTS cadastro.v_pessoafj_count;');
    }
};
