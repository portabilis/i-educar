<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('public.files_relations', function (Blueprint $table) {
            $table->index(['relation_type', 'relation_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::table('public.files_relations', function (Blueprint $table) {
            $table->dropIndex(['relation_type', 'relation_id', 'type']);
        });
    }
};
