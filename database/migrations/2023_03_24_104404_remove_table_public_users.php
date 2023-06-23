<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS users_audit ON public.users;');
        DB::unprepared('DROP TABLE IF EXISTS public.users;');
        DB::unprepared('DROP SEQUENCE IF EXISTS public.users_id_seq;');
    }
};
