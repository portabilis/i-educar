<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS notifications_audit ON public.notifications;');
        DB::unprepared('DROP TRIGGER IF EXISTS publicnotifications_audit ON public.notifications;');
    }
};
