<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('public.person_has_place', function (Blueprint $table) {
            $table->index(['person_id']);
        });
    }

    public function down(): void
    {
        Schema::table('public.person_has_place', function (Blueprint $table) {
            $table->dropIndex(['person_id']);
        });
    }
};
