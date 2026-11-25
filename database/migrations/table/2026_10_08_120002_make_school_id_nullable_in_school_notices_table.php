<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('school_notices', 'school_id')) {
            Schema::table('school_notices', function (Blueprint $table) {
                $table->unsignedInteger('school_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('school_notices', 'school_id')) {
            DB::statement('
                UPDATE school_notices sn
                SET school_id = (
                    SELECT sns.school_id
                    FROM school_notice_schools sns
                    WHERE sns.school_notice_id = sn.id
                    ORDER BY sns.id ASC
                    LIMIT 1
                )
                WHERE sn.school_id IS NULL
            ');

            Schema::table('school_notices', function (Blueprint $table) {
                $table->unsignedInteger('school_id')->nullable(false)->change();
            });
        }
    }
};
