<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('school_notices', 'school_id')) {
            DB::statement('
                INSERT INTO school_notice_schools (school_notice_id, school_id)
                SELECT id, school_id
                FROM school_notices
                WHERE school_id IS NOT NULL
            ');
        }
    }

    public function down(): void
    {
        DB::table('school_notice_schools')->truncate();
    }
};
