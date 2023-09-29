<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('public.reports_counts', function (Blueprint $table) {
            $table->boolean('authenticated')->default(false)->index();
        });
    }

    public function down(): void
    {
        Schema::table('public.reports_counts', function (Blueprint $table) {
            $table->dropColumn('authenticated');
        });
    }
};
