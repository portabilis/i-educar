<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('files_relations', function (Blueprint $table) {
            $table->renameColumn('type', 'relation_type');
        });
    }

    public function down(): void
    {
        Schema::table('files_relations', function (Blueprint $table) {
            $table->renameColumn('relation_type', 'type');
        });
    }
};
