<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_notices', function (Blueprint $table) {
            $table->unsignedInteger('institution_id')->change();
            $table->unsignedInteger('school_id')->change();
        });
    }

    public function down(): void
    {
        Schema::table('school_notices', function (Blueprint $table) {
            $table->unsignedSmallInteger('institution_id')->change();
            $table->unsignedSmallInteger('school_id')->change();
        });
    }
};
